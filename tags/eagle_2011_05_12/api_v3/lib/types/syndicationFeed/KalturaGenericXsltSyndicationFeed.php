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