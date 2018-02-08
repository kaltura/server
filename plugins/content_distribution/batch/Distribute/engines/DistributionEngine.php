<?php
/**
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */
abstract class DistributionEngine implements IDistributionEngine
{	
	/**
	 * @var int
	 */
	protected $partnerId;
	
	/**
	 * @var string
	 */
	protected $tempDirectory = null;
	
	/**
	 * @var KalturaCaptionClientPlugin
	 */
	protected $captionPlugin = null;

	/**
	 * @param string $interface
	 * @param KalturaDistributionProviderType $providerType
	 * @param KalturaDistributionJobData $data
	 * @return DistributionEngine
	 */
	public static function getEngine($interface, $providerType, KalturaDistributionJobData $data)
	{
		$engine = null;
		if($providerType == KalturaDistributionProviderType::GENERIC)
		{
			$engine = new GenericDistributionEngine();
		}
		else
		{
			$engine = KalturaPluginManager::loadObject($interface, $providerType);
		}
		
		if($engine)
		{
			$engine->setClient();
			$engine->configure($data);
		}
		
		return $engine;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngine::setClient()
	 */
	public function setClient()
	{
		$this->partnerId = KBatchBase::$kClient->getPartnerId();
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngine::setClient()
	 */
	public function configure()
	{
		$this->tempDirectory = isset(KBatchBase::$taskConfig->params->tempDirectoryPath) ? KBatchBase::$taskConfig->params->tempDirectoryPath : sys_get_temp_dir();
		if (!is_dir($this->tempDirectory)) 
			kFile::fullMkfileDir($this->tempDirectory, 0700, true);
	}

	/**
	 * @param string $entryId
	 * @return KalturaMediaEntry
	 */
	protected function getEntry($partnerId, $entryId)
	{
		KBatchBase::impersonate($partnerId);
		$entry = KBatchBase::$kClient->baseEntry->get($entryId);
		KBatchBase::unimpersonate();
		
		return $entry;
	}

	/**
	 * @param string $flavorAssetIds comma seperated
	 * @return array<KalturaFlavorAsset>
	 */
	protected function getFlavorAssets($partnerId, $flavorAssetIds)
	{
		KBatchBase::impersonate($partnerId);
		$filter = new KalturaAssetFilter();
		$filter->idIn = $flavorAssetIds;
		$flavorAssetsList = KBatchBase::$kClient->flavorAsset->listAction($filter);
		KBatchBase::unimpersonate();
		
		return $flavorAssetsList->objects;
	}

	/**
	 * @param string $thumbAssetIds comma seperated
	 * @return array<KalturaThumbAsset>
	 */
	protected function getThumbAssets($partnerId, $thumbAssetIds)
	{
		KBatchBase::impersonate($partnerId);
		$filter = new KalturaAssetFilter();
		$filter->idIn = $thumbAssetIds;
		$thumbAssetsList = KBatchBase::$kClient->thumbAsset->listAction($filter);
		KBatchBase::unimpersonate();
		return $thumbAssetsList->objects;
	}

	/**
	 * @param string $thumbAssetId
	 * @return string url
	 */
	protected function getThumbAssetUrl($thumbAssetId)
	{
		$contentDistributionPlugin = KalturaContentDistributionClientPlugin::get(KBatchBase::$kClient);
		return $contentDistributionPlugin->contentDistributionBatch->getAssetUrl($thumbAssetId);
	
//		$domain = $this->kalturaClient->getConfig()->serviceUrl;
//		return "$domain/api_v3/service/thumbAsset/action/serve/thumbAssetId/$thumbAssetId";
	}

	/**
	 * @param string $flavorAssetId
	 * @return string url
	 */
	protected function getFlavorAssetUrl($flavorAssetId)
	{
		$contentDistributionPlugin = KalturaContentDistributionClientPlugin::get(KBatchBase::$kClient);
		return $contentDistributionPlugin->contentDistributionBatch->getAssetUrl($flavorAssetId);
	}

	/**
	 * @param array<KalturaMetadata> $metadataObjects
	 * @param string $field
	 * @return array|string
	 */
	protected function findMetadataValue(array $metadataObjects, $field, $asArray = false)
	{
		$results = array();
		foreach($metadataObjects as $metadata)
		{
			$xml = new DOMDocument();
			$xml->loadXML($metadata->xml);
			$nodes = $xml->getElementsByTagName($field);
			foreach($nodes as $node)
				$results[] = $node->textContent;
		}
		
		if(!$asArray)
		{
			if(!count($results))
				return null;
				
			if(count($results) == 1)
				return reset($results);
		}
			
		return $results;
	}

	/**
	 * @param string $objectId
	 * @param KalturaMetadataObjectType $objectType
	 * @return array<KalturaMetadata>
	 */
	protected function getMetadataObjects($partnerId, $objectId, $objectType = KalturaMetadataObjectType::ENTRY, $metadataProfileId = null)
	{
		if(!class_exists('KalturaMetadata'))
			return null;
			
		KBatchBase::impersonate($partnerId);
		
		$metadataFilter = new KalturaMetadataFilter();
		$metadataFilter->objectIdEqual = $objectId;
		$metadataFilter->metadataObjectTypeEqual = $objectType;
		$metadataFilter->orderBy = KalturaMetadataOrderBy::CREATED_AT_DESC;
		
		if($metadataProfileId)
			$metadataFilter->metadataProfileIdEqual = $metadataProfileId;
		
		$metadataPager = new KalturaFilterPager();
		$metadataPager->pageSize = 1;
		$metadataListResponse = KBatchBase::$kClient->metadata->listAction($metadataFilter, $metadataPager);
		
		KBatchBase::unimpersonate();
		
		if(!$metadataListResponse->totalCount)
			throw new Exception("No metadata objects found");

		return $metadataListResponse->objects;
	}

	protected function getRemoteCaptionContentUrl($assetId)
	{
		if(!$this->captionPlugin)
		{
			$this->captionPlugin = KalturaCaptionClientPlugin::get(KBatchBase::$kClient);
		}
		try
		{
			$captionContentUrl = $this->captionPlugin->captionAsset->serve($assetId);
		}
		catch(Exception $e)
		{
			KalturaLog::err("content not served for caption id " . $assetId);
			return null;
		}
		return $captionContentUrl;
	}

	protected function putTempFile($fileName, $contentUrl, $subDirectoryName = null)
	{
		$tempDirectory = $this->getTempDirectoryForProfile($subDirectoryName);
		$fileLocation = $tempDirectory . $fileName;
		$content = file_get_contents($contentUrl);
		if (!file_exists($fileLocation) || (file_get_contents($fileLocation) !== $content))
		{
			file_put_contents($fileLocation, $content);
			chmod($fileLocation, 0600);
		}

		return $fileLocation;
	}

	protected function getTempDirectoryForProfile($subDirectoryName = null)
	{
		$tempFilePath = $this->tempDirectory . '/';
		if($subDirectoryName)
		{
			$tempFilePath .= $subDirectoryName . '/';
		}
		if (!file_exists($tempFilePath))
		{
			mkdir($tempFilePath, 0777, true);
		}
		return $tempFilePath;
	}
}
