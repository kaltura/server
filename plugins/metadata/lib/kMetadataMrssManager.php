<?php
/**
 * @package plugins.metadata
 * @subpackage lib
 */
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
	
	/* (non-PHPdoc)
	 * @see IKalturaMrssContributor::contributeToSchema()
	 */
	public function contribute(BaseObject $object, SimpleXMLElement $mrss, kMrssParameters $mrssParams = null)
	{
		$objectType = null;
		
		if($object instanceof entry)
		{
			$objectType = Metadata::TYPE_ENTRY;
		}
		else
		{
			$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaMetadataObjects');
			foreach($pluginInstances as $pluginInstance)
			{
				/* @var $pluginInstance IKalturaMetadataObjects */
				$objectType = $pluginInstance->getObjectType(get_class($object));
				if($objectType)
					break;
			}
			if(!$objectType)
				return;
		}
			
		$metadatas = MetadataPeer::retrieveAllByObject($objectType, $object->getId());
		foreach($metadatas as $metadata)
			$this->contributeMetadata($metadata, $mrss, $mrssParams);
	}
	
	/**
	 * @param Metadata $metadata
	 * @param SimpleXMLElement $mrss
	 * @param kMrssParameters $mrssParams
	 * @return SimpleXMLElement
	 */
	public function contributeMetadata(Metadata $metadata, SimpleXMLElement $mrss, kMrssParameters $mrssParams = null)
	{
		$key = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
		$xml = kFileSyncUtils::file_get_contents($key, true, false);
		$metadataXml = new SimpleXMLElement($xml);
		
		$customData = $mrss->addChild('customData');
		$customData->addAttribute('metadataId', $metadata->getId());
		$customData->addAttribute('metadataVersion', $metadata->getVersion());
		$customData->addAttribute('metadataProfileId', $metadata->getMetadataProfileId());
		$customData->addAttribute('metadataProfileVersion', $metadata->getMetadataProfileVersion());
		
		$this->contributeMetadataObject($customData, $metadataXml, $mrssParams, $metadata->getMetadataProfileId(), $metadata->getMetadataProfileVersion());
	}
	
	/**
	 * @param SimpleXMLElement $mrss
	 * @param SimpleXMLElement $metadata
	 * @param kMrssParameters $mrssParams
	 * @return SimpleXMLElement
	 */
	public function contributeMetadataObject(SimpleXMLElement $mrss, SimpleXMLElement $metadata, kMrssParameters $mrssParams = null, $metadataProfileId = null, $metadataProfileVersion = null)
	{
		$metadataObject = $mrss->addChild($metadata->getName());
		foreach($metadata->attributes() as $attributeField => $attributeValue)
			$metadataObject->addAttribute($attributeField, $attributeValue);
				
		foreach($metadata as $metadataField => $metadataValue)
		{
			if($metadataValue instanceof SimpleXMLElement && count($metadataValue)) {
				$this->contributeMetadataObject($metadataObject, $metadataValue);
			}
			else{
				$metadataObject->addChild($metadataField, kString::stringToSafeXml($metadataValue));
				//$metadataObject->addChild($metadataField, $metadataValue);
				if ($mrssParams)
				{
					$itemXpathsToExtend = $mrssParams->getItemXpathsToExtend();
					$c = new Criteria();
					$c->addAnd(MetadataProfileFieldPeer::METADATA_PROFILE_ID, $metadataProfileId, Criteria::EQUAL);
					$c->addAnd(MetadataProfileFieldPeer::METADATA_PROFILE_VERSION, $metadataProfileVersion, Criteria::EQUAL);	
					$c->addAnd(MetadataProfileFieldPeer::KEY, $metadataField, Criteria::EQUAL);		
					$metadataFieldDb = MetadataProfileFieldPeer::doSelectOne($c);
					
					if ($metadataFieldDb && is_array($itemXpathsToExtend) && in_array($metadataFieldDb->getXpath(), $itemXpathsToExtend))
					{						
						$relatedEntry = entryPeer::retrieveByPK($metadataValue);
						if ($relatedEntry)
						{
							$relatedItemField = $metadataObject->addChild($metadataField.'_item');
							$recursionMrssParams = clone $mrssParams;
							$recursionMrssParams->setItemXpathsToExtend(null);			// stop the recursion
							$relatedEntryMrss = kMrssManager::getEntryMrssXml($relatedEntry, $relatedItemField, $recursionMrssParams);
						}			
					}
				}			
			}					
		}				
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