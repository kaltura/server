<?php

class KWidevineOperationEngine extends KOperationEngine
{
	const PACKAGE_FILE_EXT = '.wvm';
	
	/**
	 * @var array
	 * batch job parameters
	 */
	private $params;
	
	/**
	 * @var string
	 * Name of the package, used as asset name in Widevine. Unique for the provider
	 */
	private $packageName;
	
	private $actualSrcAssetParams = array();
	
	private $originalEntryId;
	
	public function __construct($params, $outFilePath)
	{
		$this->params = $params;
	}
	
	/* (non-PHPdoc)
	 * @see KOperationEngine::getCmdLine()
	 */
	protected function getCmdLine() {}

	/*
	 * (non-PHPdoc)
	 * @see KOperationEngine::doOperation()
	 * 
	 * prepare PackageNotify request and send it to Widevine VOD Packager for encryption
	 */
	protected function doOperation()
	{
		KBatchBase::impersonate($this->job->partnerId);
		
		$entry = KBatchBase::$kClient->baseEntry->get($this->job->entryId);
		$this->buildPackageName($entry);
		
		KalturaLog::info('start Widevine packaging: '.$this->packageName);
		
		$drmPlugin = KalturaDrmClientPlugin::get(KBatchBase::$kClient);
		$profile = $drmPlugin->drmProfile->getByProvider(KalturaDrmProviderType::WIDEVINE);
		$wvAssetId = $this->registerAsset($profile);
		$this->encryptPackage($profile);		
		$this->updateFlavorAsset($wvAssetId);
		
		KBatchBase::unimpersonate();	
		
		return true;
	}
	
	private function registerAsset($profile)
	{
		$wvAssetId = '';
		$policy = null;
		$errorMessage = '';
		
		if($this->operator->params)
		{
			$params = explode(',', $this->operator->params);
			foreach ($params as $paramStr) 
			{
				$param = explode('=', $paramStr);
				if(isset($param[0]) && $param[0] == 'policy')
				{
					$policy = $param[1];
				}
			}
		}
		
		$wvAssetId = KWidevineBatchHelper::sendRegisterAssetRequest(
										$profile->regServerHost,
										$this->packageName,
										null,
										$profile->portal,
										$policy,
										$this->data->flavorParamsOutput->widevineDistributionStartDate,
										$this->data->flavorParamsOutput->widevineDistributionEndDate,
										$profile->iv, 
										$profile->key, 									
										$errorMessage);

		if(!$wvAssetId)
		{
			KBatchBase::unimpersonate();
			$logMessage = 'Asset registration failed, asset name: '.$this->packageName.' error: '.$errorMessage;
			KalturaLog::err($logMessage);
			throw new KOperationEngineException($logMessage);
		}
										
		KalturaLog::info('Widevine asset id: '.$wvAssetId);
		
		return $wvAssetId;
	}
	
	private function encryptPackage($profile)
	{
		$inputFiles = $this->getInputFilesList();
		$this->data->destFileSyncLocalPath = $this->data->destFileSyncLocalPath . self::PACKAGE_FILE_EXT;
				
		$returnValue = $this->executeEncryptPackageCmd($profile, $inputFiles);
		
		//try to fix source assets sync and re-try encryption
		if($returnValue == KWidevineBatchHelper::FIX_ASSET_ERROR_RETURN_CODE)
		{
			KalturaLog::info("Trying to fix input files due to mismatch error ...");
			$fixedInputFiles = $this->fixInputAssets($inputFiles);
			$returnValue = $this->executeEncryptPackageCmd($profile, $fixedInputFiles);
			if($returnValue != 0)
			{
				$this->handleEncryptPackageError($returnValue);
			}			
		}
		else if($returnValue != 0)
		{
			$this->handleEncryptPackageError($returnValue);
		}										
	}
	
	private function executeEncryptPackageCmd($profile, $inputFiles)
	{
		$returnValue = 0;
		$output = array();
		
		$cmd = KWidevineBatchHelper::getEncryptPackageCmdLine(
										$this->params->widevineExe, 
										$profile->regServerHost, 
										$profile->iv, 
										$profile->key, 
										$this->packageName, 
										$inputFiles, 
										$this->data->destFileSyncLocalPath,
										$profile->maxGop,
										$profile->portal);
										
		exec($cmd, $output, $returnValue);
		KalturaLog::info('Command execution output: '.print_r($output, true));	

		if($returnValue != 0)
		{
			if(strstr(implode(" ", $output), KWidevineBatchHelper::FIX_ASSET_ERROR))
			{
				return KWidevineBatchHelper::FIX_ASSET_ERROR_RETURN_CODE;
			}
		}
		return $returnValue;
	}
	
	private function handleEncryptPackageError($returnValue)
	{
		KBatchBase::unimpersonate();
		$errorMessage = '';
		$errorMessage = KWidevineBatchHelper::getEncryptPackageErrorMessage($returnValue);
		$logMessage = 'Package encryption failed, asset name: '.$this->packageName.' error: '.$errorMessage . ' error code: ' .$returnValue;
		KalturaLog::err($logMessage);
		throw new kTemporaryException ($logMessage);
	}
	
	private function getAssetIdsWithRedundantBitrates()
	{
		$srcAssetIds = array();
		foreach ($this->data->srcFileSyncs as $srcFileSyncDesc) 
		{
			$srcAssetIds[] = $srcFileSyncDesc->assetId;
		}		
		$srcAssetIds = implode(',', $srcAssetIds);
		
		$filter = new KalturaAssetFilter();
		$filter->entryIdEqual = $this->job->entryId;
		$filter->idIn = $srcAssetIds;
		$flavorAssetList = KBatchBase::$kClient->flavorAsset->listAction($filter);	

		$redundantAssets = array();
		if(count($flavorAssetList->objects) > 0)
		{
			$bitrates = array();			
			foreach ($flavorAssetList->objects as $flavorAsset) 
			{
				/* @var $flavorAsset KalturaFlavorAsset */
				if(in_array($flavorAsset->bitrate, $bitrates))
					$redundantAssets[] = $flavorAsset->id;
				else 
					$bitrates[] = $flavorAsset->bitrate;
			}
		}		
		return $redundantAssets;
	}
	
	private function getInputFilesList()
	{		
		$redundantAssets = $this->getAssetIdsWithRedundantBitrates();
		$inputFilesArr = array();
		
		foreach ($this->data->srcFileSyncs as $srcFileSyncDescriptor) 
		{
			if(in_array($srcFileSyncDescriptor->assetId, $redundantAssets))
			{
				KalturaLog::info('Skipping flavor asset due to redundant bitrate: '.$srcFileSyncDescriptor->assetId);
			}
			else 
			{
				$inputFilesArr[] = $srcFileSyncDescriptor->actualFileSyncLocalPath;
				$this->actualSrcAssetParams[] = $srcFileSyncDescriptor->assetParamsId;
			}
		}		
		return implode(',', $inputFilesArr);
	}
	
	private function buildPackageName($entry)
	{	
		$flavorAssetId = $this->data->flavorAssetId;
		$this->originalEntryId = $this->job->entryId;
			
		if($entry->replacedEntryId)
		{
			$this->originalEntryId = $entry->replacedEntryId;
			$filter = new KalturaAssetFilter();
			$filter->entryIdEqual = $entry->replacedEntryId;
			$filter->tagsLike = 'widevine'; 
			$flavorAssetList = KBatchBase::$kClient->flavorAsset->listAction($filter);
			
			if(count($flavorAssetList->objects) > 0)
			{
				$replacedFlavorParamsId = $this->data->flavorParamsOutput->flavorParamsId;
				foreach ($flavorAssetList->objects as $flavorAsset) 
				{
					/* @var $flavorAsset KalturaFlavorAsset */
					if($flavorAsset->flavorParamsId == $replacedFlavorParamsId)
					{
						$flavorAssetId = $flavorAsset->id;
						break;
					}
				}
			}
		}
		
		$this->packageName = $this->originalEntryId.'_'.$flavorAssetId;
	}
	
	private function updateFlavorAsset($wvAssetId = null)
	{
		$updatedFlavorAsset = new KalturaWidevineFlavorAsset();
		if($wvAssetId)
			$updatedFlavorAsset->widevineAssetId = $wvAssetId;
		$updatedFlavorAsset->actualSourceAssetParamsIds = implode(',', $this->actualSrcAssetParams);		
		$wvDistributionStartDate = $this->data->flavorParamsOutput->widevineDistributionStartDate;
		$wvDistributionEndDate = $this->data->flavorParamsOutput->widevineDistributionEndDate;
		$updatedFlavorAsset->widevineDistributionStartDate = $wvDistributionStartDate;
		$updatedFlavorAsset->widevineDistributionEndDate = $wvDistributionEndDate;
		KBatchBase::$kClient->flavorAsset->update($this->data->flavorAssetId, $updatedFlavorAsset);		
	}
	
	private function fixInputAssets($inputFiles)
	{
		$localTmpPath = dirname($this->data->destFileSyncLocalPath);
		$fixedInputFiles = array();
		$inputFiles = explode(',', $inputFiles);
		$returnValue = 0;
		$output = array();
		foreach ($inputFiles as $inputFile) 
		{
			$fixedInputFile = $localTmpPath.'/'.basename($inputFile);
			$cmd = KWidevineBatchHelper::getFixAssetCmdLine($this->params->ffmpegCmd, $inputFile, $fixedInputFile);
			$lastLine = exec($cmd, $output, $returnValue);
			
			KalturaLog::info('Command execution output: '.print_r($output, true));
		
			if($returnValue != 0)
			{
				KBatchBase::unimpersonate();
				$logMessage = 'Asset fix failed: '.$inputFile.' error: '.$lastLine;
				KalturaLog::err($logMessage);
				throw new KOperationEngineException($logMessage);
			}										
			$fixedInputFiles[] = $fixedInputFile;
		}
		
		return implode(',', $fixedInputFiles);
	}
	
}