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
	 * @var KalturaExtendingItemMrssParameterArray
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
	
	public function fromObject($source_object, IResponseProfile $responseProfile = null)
	{
		parent::fromObject($source_object, $responseProfile);

		$key = $source_object->getSyncKey(genericSyndicationFeed::FILE_SYNC_SYNDICATION_FEED_XSLT);
		$this->xslt = kFileSyncUtils::file_get_contents($key, true, false);

		$mrssParams = $source_object->getMrssParameters();
		$this->itemXpathsToExtend = new KalturaExtendingItemMrssParameterArray();
		if ($mrssParams && $mrssParams->getItemXpathsToExtend())
		{
			$this->itemXpathsToExtend = KalturaExtendingItemMrssParameterArray::fromDbArray($mrssParams->getItemXpathsToExtend());
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
			$mrssParams->setItemXpathsToExtend($itemXpathsToExtend);
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
		
		if($this->xslt)
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
		
		if($this->xslt)
			kSyndicationFeedManager::validateXsl($this->xslt);
		
		return parent::toUpdatableObject($object_to_fill, $props_to_skip );
	}
}