<?php
abstract class DistributionEngine implements IDistributionEngine
{
	/**
	 * @var KalturaClient
	 */
	protected $kalturaClient = null;
	
	/**
	 * @var int
	 */
	protected $partnerId;
	
	/**
	 * @param string $interface
	 * @param KalturaDistributionProviderType $providerType
	 * @param KSchedularTaskConfig $taskConfig
	 * @param KalturaDistributionJobData $data
	 * @return DistributionEngine
	 */
	public static function getEngine($interface, $providerType, KalturaClient $kalturaClient, KSchedularTaskConfig $taskConfig, KalturaDistributionJobData $data)
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
			$engine->setClient($kalturaClient);
			$engine->configure($taskConfig, $data);
		}
		
		return $engine;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngine::setClient()
	 */
	public function setClient(KalturaClient $kalturaClient)
	{
		$this->kalturaClient = $kalturaClient;
		
		$config = $this->kalturaClient->getConfig();
		$this->partnerId = $config->partnerId;
	}
	
	public function unimpersonate()
	{
		$config = $this->kalturaClient->getConfig();
		$config->partnerId = $this->partnerId;
		$this->kalturaClient->setConfig($config);
	}
	
	public function impersonate($partnerId)
	{
		$config = $this->kalturaClient->getConfig();
		$config->partnerId = $partnerId;
		$this->kalturaClient->setConfig($config);
	}

	/**
	 * @param string $entryId
	 * @return KalturaMediaEntry
	 */
	protected function getEntry($partnerId, $entryId)
	{
		$this->impersonate($partnerId);
		$entry = $this->kalturaClient->baseEntry->get($entryId);
		$this->unimpersonate();
		
		return $entry;
	}

	/**
	 * @param string $flavorAssetIds comma seperated
	 * @return array<KalturaFlavorAsset>
	 */
	protected function getFlavorAssets($partnerId, $flavorAssetIds)
	{
		$this->impersonate($partnerId);
		$filter = new KalturaAssetFilter();
		$filter->idIn = $flavorAssetIds;
		$flavorAssetsList = $this->kalturaClient->flavorAsset->listAction($filter);
		$this->unimpersonate();
		return $flavorAssetsList->objects;
	}

	/**
	 * @param string $thumbAssetIds comma seperated
	 * @return array<KalturaThumbAsset>
	 */
	protected function getThumbAssets($partnerId, $thumbAssetIds)
	{
		$this->impersonate($partnerId);
		$filter = new KalturaAssetFilter();
		$filter->idIn = $thumbAssetIds;
		$thumbAssetsList = $this->kalturaClient->thumbAsset->listAction($filter);
		$this->unimpersonate();
		return $thumbAssetsList->objects;
	}

	/**
	 * @param string $thumbAssetId
	 * @return string url
	 */
	protected function getThumbAssetUrl($thumbAssetId)
	{
		$domain = $this->kalturaClient->getConfig()->serviceUrl;
		$ks = $this->kalturaClient->getKs();
		return "$domain/api_v3/service/thumbAsset/action/serve/ks/$ks/thumbAssetId/$thumbAssetId";
	}

	/**
	 * @param string $flavorAssetId
	 * @return string url
	 */
	protected function getFlavorAssetUrl($flavorAssetId)
	{
		$this->impersonate($this->partnerId);
		$url = $this->kalturaClient->flavorAsset->getDownloadUrl($flavorAssetId, true);
		$this->unimpersonate();
		return $url;
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
	protected function getMetadataObjects($partnerId, $objectId, $objectType = KalturaMetadataObjectType::ENTRY)
	{
		if(!class_exists('KalturaMetadata'))
			return null;
			
		$this->impersonate($partnerId);
		
		$metadataFilter = new KalturaMetadataFilter();
		$metadataFilter->objectIdEqual = $objectId;
		$metadataFilter->metadataObjectTypeEqual = $objectType;
		$metadataFilter->orderBy = KalturaMetadataOrderBy::CREATED_AT_DESC;
		$metadataPager = new KalturaFilterPager();
		$metadataPager->pageSize = 1;
		$metadataListResponse = $this->kalturaClient->metadata->listAction($metadataFilter, $metadataPager);
		
		$this->unimpersonate();
		
		if(!$metadataListResponse->totalCount)
			throw new Exception("No metadata objects found");

		return $metadataListResponse->objects;
	}
}