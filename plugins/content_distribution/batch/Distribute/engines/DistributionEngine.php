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
			$engine = new GenericDistributionEngine($kalturaClient);
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
	 * @return KalturaBaseEntry
	 */
	protected function getEntry($entryId)
	{
		return $this->kalturaClient->baseEntry->get($entryId);
	}

	/**
	 * @param string $entryId
	 * @return KalturaMetadata
	 */
	protected function getEntryMetadata($entryId)
	{
		if(!class_exists('KalturaMetadata'))
			return null;
			
		$metadataFilter = new KalturaMetadataFilter();
		$metadataFilter->objectIdEqual = $entryId;
		$metadataFilter->metadataObjectTypeEqual = KalturaMetadataObjectType::ENTRY;
		$metadataFilter->orderBy = KalturaMetadataOrderBy::CREATED_AT_DESC;
		$metadataPager = new KalturaFilterPager();
		$metadataPager->pageSize = 1;
		$metadataListResponse = $this->kalturaClient->metadata->listAction($metadataFilter, $metadataPager);
		if(!$metadataListResponse->totalCount)
			throw new Exception("No metadata objects found");

		return reset($metadataListResponse->objects);
	}
}