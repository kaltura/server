<?php
class kMetadataMrssManager implements IKalturaMrssContributor
{
	/**
	 * @var kMetadataMrssManager
	 */
	protected static $instance;
	
	protected function __construct()
	{
	}
	
	/**
	 * @return kMetadataMrssManager
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new kMetadataMrssManager();
			
		return self::$instance;
	}
	
	/**
	 * @param entry $entry
	 * @param SimpleXMLElement $mrss
	 * @return SimpleXMLElement
	 */
	public function contribute(entry $entry, SimpleXMLElement $mrss)
	{
		$metadatas = MetadataPeer::retrieveAllByObject(metadata::TYPE_ENTRY, $entry->getId());
		foreach($metadatas as $metadata)
			$this->contributeMetadata($metadata, $mrss);
	}
	
	/**
	 * @param Metadata $metadata
	 * @param SimpleXMLElement $xmlElement
	 * @return SimpleXMLElement
	 */
	public function contributeMetadata(Metadata $metadata, SimpleXMLElement $mrss)
	{
		$key = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
		$xml = kFileSyncUtils::file_get_contents($key, true, false);
		
		$customData = $mrss->addChild('customData', $xml);
		$customData->addAttribute('metadataId', $metadata->getId());
		$customData->addAttribute('metadataVersion', $metadata->getVersion());
		$customData->addAttribute('metadataProfileId', $metadata->getMetadataProfileId());
		$customData->addAttribute('metadataProfileVersion', $metadata->getMetadataProfileVersion());
	}

	/* (non-PHPdoc)
	 * @see IKalturaBase::getInstance()
	 */
	public function getInstance($interface)
	{
		if($this instanceof $interface)
			return $this;
			
		$plugin = KalturaPluginManager::getPluginInstance(MetadataPlugin::getPluginName());		
		if($plugin)
			return $plugin->getInstance($interface);
		
		return null;
	}
	
}