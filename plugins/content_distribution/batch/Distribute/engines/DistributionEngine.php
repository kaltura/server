<?php
abstract class DistributionEngine implements IDistributionEngine
{
	/**
	 * @var KalturaClient
	 */
	protected $kalturaClient = null;
	
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
	}

	/**
	 * @param string $entryId
	 * @return KalturaMediaEntry
	 */
	protected function getEntry($entryId)
	{
		return $this->kalturaClient->baseEntry->get($entryId);
	}

	/**
	 * @param string $flavorAssetIds comma seperated
	 * @return array<KalturaFlavorAsset>
	 */
	protected function getFlavorAssets($flavorAssetIds)
	{
		$filter = new KalturaAssetFilter();
		$filter->idIn = $flavorAssetIds;
		$flavorAssetsList = $this->kalturaClient->flavorAsset->listAction($filter);
		return $flavorAssetsList->objects;
	}

	/**
	 * @param string $thumbAssetIds comma seperated
	 * @return array<KalturaThumbAsset>
	 */
	protected function getThumbAssets($thumbAssetIds)
	{
		$filter = new KalturaAssetFilter();
		$filter->idIn = $thumbAssetIds;
		$thumbAssetsList = $this->kalturaClient->thumbAsset->listAction($filter);
		return $thumbAssetsList->objects;
	}

	/**
	 * @param string $thumbAssetId
	 * @return string url
	 */
	protected function getThumbAssetUrl($thumbAssetId)
	{
		$domain = $this->kalturaClient->getConfig()->serviceUrl;
		return "$domain/service/thumbAsset/action/serve/thumbAssetId/$thumbAssetId";
	}

	/**
	 * @param string $flavorAssetId
	 * @return string url
	 */
	protected function getFlavorAssetUrl($flavorAssetId)
	{
//		TODO
//		$domain = $this->kalturaClient->getConfig()->serviceUrl;
//		return "$domain/service/flavorAsset/action/serve/flavorAssetId/$flavorAssetId";
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
	protected function getMetadataObjects($objectId, $objectType = KalturaMetadataObjectType::ENTRY)
	{
		if(!class_exists('KalturaMetadata'))
			return null;
			
		$metadataFilter = new KalturaMetadataFilter();
		$metadataFilter->objectIdEqual = $objectId;
		$metadataFilter->metadataObjectTypeEqual = $objectType;
		$metadataFilter->orderBy = KalturaMetadataOrderBy::CREATED_AT_DESC;
		$metadataPager = new KalturaFilterPager();
		$metadataPager->pageSize = 1;
		$metadataListResponse = $this->kalturaClient->metadata->listAction($metadataFilter, $metadataPager);
		if(!$metadataListResponse->totalCount)
			throw new Exception("No metadata objects found");

		return $metadataListResponse->objects;
	}
}