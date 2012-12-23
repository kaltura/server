<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaGenericXsltSyndicationFeed extends KalturaGenericSyndicationFeed
{
	/**
	*
	* @var string
	*/
	public $xslt;

	/**
	 * This parameter determines which custom metadata fields of type related-entry should be
	 * expanded to contain the kaltura MRSS feed of the related entry. Related-entry fields not
	 * included in this list will contain only the related entry id.
	 * This property contains a list xPaths in the Kaltura MRSS.
	 * 
	 * @var KalturaStringArray
	 */
	public $itemXpathsToExtend;
	
	private static $mapBetweenObjects = array
	(
   		
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
        
    function __construct()
	{
		$this->type = KalturaSyndicationFeedType::KALTURA_XSLT;
	}
	
	public function fromObject($source_object)
	{
		parent::fromObject($source_object);

		$key = $source_object->getSyncKey(genericSyndicationFeed::FILE_SYNC_SYNDICATION_FEED_XSLT);
		$this->xslt = kFileSyncUtils::file_get_contents($key, true, false);

		$mrssParams = $source_object->getMrssParameters();
		if ($mrssParams && $mrssParams->getItemXpathsToExtend())
		{
			$xpaths = array();
			foreach ($mrssParams->getItemXpathsToExtend() as $itemXPathToExtend)
			{
				/* @var $itemXPathToExtend kExtendingItemMrssParameter */
				$xpaths[] = $itemXPathToExtend->getXpath();
			}
			$this->itemXpathsToExtend = KalturaStringArray::fromDbArray($xpaths);
		}
		else
		{
			$this->itemXpathsToExtend = new KalturaExtendingItemMrssParameterArray();
		}
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		parent::toObject($dbObject, $skip);
		
		$mrssParams = $dbObject->getMrssParameters();
		if (!$mrssParams)
		{
			$mrssParams = new kMrssParameters;
		}
		
		if ($this->itemXpathsToExtend)
		{
			$itemXpathsToExtend = $this->itemXpathsToExtend->toObjectsArray();
			$itemXpaths = array();
			foreach ($itemXpathsToExtend as $itemXPathToExtend)
			{
				$itemXPath = new kExtendingItemMrssParameter();
				$itemXPath->setXpath($itemXPathToExtend);
				$itemXPath->setExtensionMode(MrssExtensionMode::APPEND);
				$identifier = new kEntryIdentifier();
				$identifier->setExtendedFeatures("");
				$identifier->setIdentifier(EntryIdentifierField::ID);
				$itemXPath->setIdentifier($identifier);
				$itemXpaths[] = $itemXPath;
			}
			$mrssParams->setItemXpathsToExtend($itemXpaths);
		}
		
		$dbObject->setMrssParameters($mrssParams);
	}
	
	/**
	 * @param SyndicationDistributionProfile $object_to_fill
	 * @param array $props_to_skip
	 * @return genericSyndicationFeed
	 */
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new genericSyndicationFeed();
		
		kSyndicationFeedManager::validateXsl($this->xslt);	
		
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	/**
	 * @param SyndicationDistributionProfile $object_to_fill
	 * @param array $props_to_skip
	 * @return genericSyndicationFeed
	 */
	public function toUpdatableObject ( $object_to_fill , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new genericSyndicationFeed();
		
		kSyndicationFeedManager::validateXsl($this->xslt);
		
		return parent::toUpdatableObject($object_to_fill, $props_to_skip );
	}
}