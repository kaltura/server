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
		
		KalturaLog::debug('start Widevine packaging: '.$this->packageName);
		
		$wvAssetId = $this->registerAsset();
		$this->encryptPackage();		
		$this->updateFlavorAsset($wvAssetId);
		
		KBatchBase::unimpersonate();	
		
		return true;
	}
	
	private function registerAsset()
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
										$this->params->wvLicenseServerUrl,
										$this->packageName,
										null,
										$this->params->portal,
										$policy,
										$this->data->flavorParamsOutput->widevineDistributionStartDate,
										$this->data->flavorParamsOutput->widevineDistributionEndDate,
										$errorMessage);

		if(!$wvAssetId)
		{
			KBatchBase::unimpersonate();
			$logMessage = 'Asset registration failed, asset name: '.$this->packageName.' error: '.$errorMessage;
			KalturaLog::err($logMessage);
			throw new KOperationEngineException($logMessage);
		}
										
		KalturaLog::debug('Widevine asset id: '.$wvAssetId);
		
		return $wvAssetId;
	}
	
	private function encryptPackage()
	{
		$returnValue = 0;
		$output = array();
		
		$inputFiles = $this->getInputFilesList();
		$this->data->destFileSyncLocalPath = $this->data->destFileSyncLocalPath . self::PACKAGE_FILE_EXT;
				
		$cmd = KWidevineBatchHelper::getEncryptPackageCmdLine(
										$this->params->widevineExe, 
										$this->params->wvLicenseServerUrl, 
										$this->params->iv, 
										$this->params->key, 
										$this->packageName, 
										$inputFiles, 
										$this->data->destFileSyncLocalPath,
										$this->params->gop,
										$this->params->portal);
										
		exec($cmd, $output, $returnValue);
		KalturaLog::debug('Command execution output: '.print_r($output));
		
		if($returnValue != 0)
		{
			KBatchBase::unimpersonate();
			$errorMessage = '';
			$errorMessage = KWidevineBatchHelper::getEncryptPackageErrorMessage($returnValue);
			$logMessage = 'Package encryption failed, asset name: '.$this->packageName.' error: '.$errorMessage;
			KalturaLog::err($logMessage);
			throw new KOperationEngineException($logMessage);
		}										
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
				KalturaLog::debug('Skipping flavor asset due to redundant bitrate: '.$srcFileSyncDescriptor->assetId);
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
	
}