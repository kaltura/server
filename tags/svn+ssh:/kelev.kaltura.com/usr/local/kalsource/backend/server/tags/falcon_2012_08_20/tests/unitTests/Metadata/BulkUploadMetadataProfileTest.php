<?php
require_once(dirname(__FILE__) . "/../../lib/KalturaClient.php");
require_once('config/config.php');

class BulkUploadMetadataProfileTest extends PHPUnit_Framework_TestCase 
{
	private $client = null;
	private $metadataProfiles = array(); 
	
	public function testBulkUploadMetadataProfile() 
	{
		echo 'testBulkUploadMetadataProfile - Start' . PHP_EOL;
		
		$this->initClient();
		
		$metadataProfile = $this->metadataProfileAdd(Config::METADATA_NAME, Config::METADATA_XSD_DATA_ADD);
		$this->metadataProfiles[$metadataProfile->id] = Config::METADATA_NAME;
		
		$metadataProfile = $this->metadataProfileAdd(Config::METADATA_NAME_2, Config::METADATA_XSD_DATA_ADD);
		$this->metadataProfiles[$metadataProfile->id] = Config::METADATA_NAME_2;
		
		$bulkuploadId = $this->bulkUploadAdd(Config::BULK_UPLOAD_FILE);
		
		$this->sleepWhileBulkIsNotFinished($bulkuploadId);
		$entryId = $this->getEntryByReferanceId(Config::ENTRY_REFERENCE_ID);	

		$this->assetMetadatas($entryId);
	}
	
	private function assetMetadatas($entryId)
	{
		echo 'assetMetadatas entryId:' . $entryId . PHP_EOL;
		
		$filter = new KalturaMetadataFilter();
		$filter->objectIdEqual = $entryId;
		
		try{
			$metadatas = $this->client->metadata->listAction($filter);
		}
		catch (Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in assetMetadatas - metadata->list - ' . $ex->getMessage());
		}
		
		echo 'assetMetadatas - metadata objects: ' . count($metadatas->objects) . PHP_EOL;
		
		foreach ($metadatas->objects as $metadata)
		{
			if(!isset($this->metadataProfiles[$metadata->metadataProfileId]))
				$this->assertTrue ( false, 'Exception in assetMetadatas - unexpected metadata profile id ' . $metadata->metadataProfileId);
				
			$find = array ("\r\n", "\n", "\r");
			$replace = '';
			$expectedMetaData= str_replace($find, $replace, Config::$metadatasByProfileSystemName[$this->metadataProfiles[$metadata->metadataProfileId]]);
			$metadataXml= str_replace($find, $replace, $metadata->xml);			
			
			$this->assertEquals($metadataXml, $expectedMetaData, 'Metadata was not set as expected for metadata id: ' . $metadata->id);
		}
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
	
	private function metadataProfileAdd($profileName, $xsdData)
	{
		echo 'metadataProfileAdd' . PHP_EOL;
		
		$metadataProfile = new KalturaMetadataProfile();
		$metadataProfile->metadataObjectType = KalturaMetadataObjectType::ENTRY;
		$metadataProfile->name = $profileName;
		$metadataProfile->systemName = $profileName;
		$metadataProfile->createMode = KalturaMetadataProfileCreateMode::KMC;
		
		try{
			$profile = $this->client->metadataProfile->add($metadataProfile, $xsdData);
		}
		catch (Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in metadataProfileAdd - metadataProfile->add - ' . $ex->getMessage());
		}
		
		echo 'metadataProfileAdd: added metadata profile: ' . $profile->id . PHP_EOL;
		
		return $profile;
	}
	
	private function mediaList()
	{
		echo 'mediaList' . PHP_EOL;
		
		try{
			$entries = $this->client->media->listAction();
		}
		catch (Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in mediaList - media->listAction - ' . $ex->getMessage());
		}
		
		echo 'mediaList return [' . count($entries->objects) . '] entries' . PHP_EOL;
		
		return $entries->objects;
	}
	
	private function metadataAdd($profileId, $entryId, $entryMetadata, $entryTransformedMetadata)
	{
		echo 'metadataAdd' . PHP_EOL;
		$objectType = KalturaMetadataObjectType::ENTRY; 
		
		try{
			$metadata = $this->client->metadata->add($profileId, $objectType, $entryId, $entryMetadata);
		}
		catch (Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in metadataAdd - metadata->add - ' . $ex->getMessage());
		}
		
		$this->metadatas[] = array(Config::METADATA => $metadata, Config::ENTRY_METADATA_TRANSFORMED => $entryTransformedMetadata);
	}
	
	private function metadataProfileUpdate($profileId, $xsdData)
	{
		echo 'metadataProfileUpdate' . PHP_EOL;
		$metadataProfile = new KalturaMetadataProfile();
		
		try{
			$profile = $this->client->metadataProfile->update($profileId, $metadataProfile, $xsdData);
		}
		catch (Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in metadataProfileUpdate - metadataProfile->update - ' . $ex->getMessage());
		}
		
		echo 'metadataProfileUpdate - update profile id: ' . $profile->id . PHP_EOL;
		
		return $profile;
	}

	private function bulkUploadAdd($csvFileData)
	{
		echo 'bulkUploadAdd' . PHP_EOL;
		
		$bulkUploadType = KalturaBulkUploadType::XML;

		try{
			$bulkJobId = $this->client->bulkUpload->add(-1, $csvFileData, $bulkUploadType);
		}
		catch (Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in bulkUploadAdd - bulkUpload->add - ' . $ex->getMessage());
		}
		return $bulkJobId->id;
	}
	
	private function sleepWhileBulkIsNotFinished($bulkuploadId)
	{
		echo 'sleepWhileBulkIsNotFinished bulkuploadId: ' . $bulkuploadId . PHP_EOL;
		while(true)
		{
			try{
		        $output = $this->client->bulkUpload->get($bulkuploadId);
			}catch (Exception $ex)
			{
				$this->assertTrue ( false, 'Exception in checkResults - bulkUpload->get - ' . $ex->getMessage());
			}
			
		        
	        if($output->status == KalturaBatchJobStatus::FAILED
				||	$output->status == KalturaBatchJobStatus::ABORTED
				||	$output->status == KalturaBatchJobStatus::FATAL
				||	$output->status == KalturaBatchJobStatus::DONT_PROCESS) 
				$this->assertTrue ( false, 'Exception in bulkUpload->status: ' . $output->status);
						
				if ($output->status == KalturaBatchJobStatus::FINISHED) 
					break;
					
			echo 'sleep...' . PHP_EOL;
			sleep(10);
		}	
	}
	
	private function getEntryByReferanceId($referanceId)
	{
		echo 'getEntryByReferanceId' . PHP_EOL;
		
		$filter = new KalturaBaseEntryFilter();
		$filter->referenceIdEqual = $referanceId;
		try{
			$entries = $this->client->baseEntry->listAction($filter);
		}
		catch (Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in getEntryByReferanceId - baseEntry->listAction - ' . $ex->getMessage());
		}
		
		echo 'mediaList return [' . count($entries->objects) . '] entries' . PHP_EOL;
		
		return $entries->objects[0]->id;
	}
}
