<?php
require_once(dirname(__FILE__) . "/../../lib/KalturaClient.php");
require_once('config/config.php');

class IngestionTest extends PHPUnit_Framework_TestCase 
{
	private $client = null;
	
	public function testIngestion() 
	{
		echo 'Ingestion Test - Start' . PHP_EOL;
		
		$this->initClient();
		//$this->initConversionProfile();
		
		foreach (Config::$assets as $asset)
			$this->uploadMedia($asset[Config::ENTRY_NAME], $asset[config::ENTRY_TYPE], $asset[config::ENTRY_FILE_DATA], Config::TESTS_DATA . $asset[config::ENTRY_FILE_DATA]);		
		
	}
	
	private function initClient()
	{
		echo 'initClient' . PHP_EOL;
		
		if ($this->client)
			return;
			
		try {
			$config = new KalturaConfiguration(Config::PARTNER_ID);
			$config->serviceUrl = Config::SERVER_URL;
			$client = new KalturaClient($config);
			$ks = $client->session->start(Config::PARTNER_ADMIN_SECRET, Config::PARTNER_USER_ID, KalturaSessionType::ADMIN, Config::PARTNER_ID);
			$client->setKs($ks);
		}
		catch (Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in session->start - ' . $ex->getMessage());
		}
		
		$this->client = $client;
	}
	
	private function initConversionProfile()
	{
		echo 'initConversionProfile' . PHP_EOL;
		
		$conversionProfileFilter = new KalturaConversionProfileFilter();
		$conversionProfileFilter->systemNameEqual =  Config::FULL_CONVERSION_PROFILE_NAME;
		
		try{
			$conversionProfiles = $this->client->conversionProfile->listAction($conversionProfileFilter);
		}
		catch(Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in conversionProfile->list - ' . $ex->getMessage());
		}
		
		if (count($conversionProfiles->objects)){
			foreach ($conversionProfiles->objects as $conversionProfile){
				if ($conversionProfile->isDefault){
					echo 'full conversion profile exists: ' . $conversionProfile->id . PHP_EOL;
					return;
				}
					
			}
		}
		
		return;
		
		try{
			$flavorParams = $this->client->flavorParams->listAction();
		}
		catch(Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in flavorParams->listAction - ' . $ex->getMessage());
		}
		
		$flavorParamsIds = array();
		
		foreach ($flavorParams->objects as $flavorParam)
		{
			$flavorParamsIds[] = $flavorParam->id;
		}
		
		$conversionProfile = new KalturaConversionProfile();
		$conversionProfile->name = Config::FULL_CONVERSION_PROFILE_NAME;
		$conversionProfile->systemName =  Config::FULL_CONVERSION_PROFILE_NAME;
		$conversionProfile->flavorParamsIds = implode(',', $flavorParamsIds);
		$conversionProfile->isDefault = KalturaNullableBoolean::TRUE_VALUE;
		
		try{
			$conversionProfile = $this->client->conversionProfile->add($conversionProfile);
		}catch (Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in conversionProfile->add - ' . $ex->getMessage());
		}
		
		$conversionProfileAssetParamsFilter = new KalturaConversionProfileAssetParamsFilter();
		$conversionProfileAssetParamsFilter->conversionProfileIdEqual = $conversionProfile->id;
		
		try{
			$ConversionProfileAssetParams = $this->client->conversionProfileAssetParams->listAction($conversionProfileAssetParamsFilter);
		}catch (Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in conversionProfileAssetParams->listAction - ' . $ex->getMessage());
		}
		
		foreach ($ConversionProfileAssetParams->objects as $ConversionProfileAssetParam)
		{
			$assetParam = new kalturaConversionProfileAssetParams();
			$assetParam->readyBehavior = KalturaFlavorReadyBehaviorType::REQUIRED;
			
			try{
				$this->client->conversionProfileAssetParams->update($ConversionProfileAssetParam->conversionProfileId, $ConversionProfileAssetParam->assetParamsId, $assetParam );
			}catch (Exception $ex)
			{
				$this->assertTrue ( false, 'Exception in conversionProfileAssetParams->update - ' . $ex->getMessage());
			}
		}
		
	}
	
	private function uploadMedia($entryName, $entryType, $entryFileName, $entryFileData)
	{
		echo 'uploadMedia' . PHP_EOL;
		
		$entry = $this->mediaAdd($entryName, $entryType);
		$uploadToken = $this->uploadTokenAdd($entryFileName);
		$entry = $this->mediaUpdateContent($entry, $entryType, $uploadToken);
		echo $entry->id .PHP_EOL;
		var_dump($uploadToken);
		exit;
		//$uploadToken = $this->uploadTokenUpload($uploadToken, $entryFileData);
		//$this->entryReadiness($entry);
		//$this->assetsReadiness($entry);
		echo $entry->id;
		return $entry;
	}
		
	private function mediaAdd($entryName, $entryType)
	{
		echo 'mediaAdd' . PHP_EOL;
		
		$entry = new KalturaMediaEntry();
		$entry->name = $entryName;
		$entry->mediaType = $entryType;
		
		try{
			$entry = $this->client->media->add($entry);
		}
		catch (Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in media->add - ' . $ex->getMessage());
		}
		
		//TODO - check the result
		
		return $entry;
	}
	
	private function uploadTokenAdd($entryName)
	{
		echo 'uploadTokenAdd' . PHP_EOL;
		
		$uploadToken = new KalturaUploadToken();
		$uploadToken->fileName = $entryName;
		
		try{
			$uploadToken = $this->client->uploadToken->add($uploadToken);
		}
		catch (Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in uploadToken->add - ' . $ex->getMessage());
		}
		
		//TODO - check the results
		
		return $uploadToken;
	}
	
	private function mediaUpdateContent($entry, $entryType,$uploadToken)
	{
		echo 'mediaUpdateContent' . PHP_EOL;
		
		$entryId = $entry->id;
		
		$uploadTokenResouce = new KalturaUploadedFileTokenResource();
		$uploadTokenResouce->token = $uploadToken->id;
		$resource = $uploadTokenResouce;
		$conversionProfileId = 0;
		
		if ($entryType != KalturaMediaType::IMAGE)
		{
			$assetParamsResourceContainer = new KalturaAssetParamsResourceContainer();
			$assetParamsResourceContainer->assetParamsId = 0;
			$assetParamsResourceContainer->resource = $uploadTokenResouce;
			
			$resourceContainers = new KalturaAssetsParamsResourceContainers();
			$resourceContainers->resources = array($assetParamsResourceContainer);
			
			$resource = $resourceContainers;
		}
		
		try{
			$entry = $this->client->media->updateContent($entryId, $resource, $conversionProfileId);
		}
		catch (Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in media->updateContent - ' . $ex->getMessage());
		}
		
		
		//TODO - check the results
		return $entry;
		
		
	}
	
	private function uploadTokenUpload($uploadToken, $entryFileData)
	{
		echo 'uploadTokenUpload' . PHP_EOL;
		
		$uploadTokenId = $uploadToken->id; 
		$fileData = $entryFileData;
		$resume = false;
		$finalChunk  = true;
		$resumeAt = -1;
		
		try{
			$uploadToken = $this->client->uploadToken->upload($uploadTokenId, $fileData, $resume, $finalChunk, $resumeAt);
		}
		catch (Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in uploadToken->upload - ' . $ex->getMessage());
		}
		
		//TODO - check the results
		
		return $uploadToken;
	}
	
	private function entryReadiness($entry)
	{
		echo 'entryReadiness' . PHP_EOL;
		
		do 
		{
			sleep(20);
			try{
				$updatedEntry = $this->client->media->get($entry->id);
				echo 'entryReadiness - entry status: ' . $updatedEntry->status . PHP_EOL;
			}
			catch (Exception $ex)
			{
				$this->assertTrue ( false, 'Exception in entryReadiness - media->get - ' . $ex->getMessage());
			}
		}
		while (	$updatedEntry->status != KalturaEntryStatus::ERROR_IMPORTING 
			&&	$updatedEntry->status != KalturaEntryStatus::ERROR_CONVERTING
			&&	$updatedEntry->status != KalturaEntryStatus::READY
			&&	$updatedEntry->status != KalturaEntryStatus::DELETED); // while not in final status
			
		$this->assertEquals((int)KalturaEntryStatus::READY, $updatedEntry->status, 'entry id [' . $updatedEntry->id . '] status [' . $updatedEntry->status . ']');		
			
		//TODO - check the results
	}
	
	private function assetsReadiness($entry)
	{
		$assetFilter = new KalturaAssetFilter();
		$assetFilter->entryIdEqual = $entry->id;
		
		try{
			$assets = $this->client->flavorAsset->listAction($assetFilter);
		}
		catch (Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in assetsReadiness - flavorAsset->list - ' . $ex->getMessage());
		}

		foreach ($assets->objects as $asset)
		{
			$this->assertArrayHasKey($asset->status, array(KalturaAssetStatus::READY => KalturaAssetStatus::READY, KalturaFlavorAssetStatus::NOT_APPLICABLE => KalturaFlavorAssetStatus::NOT_APPLICABLE), 'Asset [ ' . $asset->id . ' ] status is [' . $asset->status . ']');
		}
	}
}
