<?php
/**
 * @package plugins.captionSearch
 * @subpackage api.objects
 */
class KalturaCaptionAssetItem extends KalturaObject
{
	/**
	 * The Caption Asset object
	 * 
	 * @var KalturaCaptionAsset
	 */
	public $asset;
	
	/**
	 * The entry object
	 * 
	 * @var KalturaBaseEntry
	 */
	public $entry;
	
	/**
	 * @var int
	 */
	public $startTime;
	
	/**
	 * @var int
	 */
	public $endTime;
	
	/**
	 * @var string
	 */
	public $content;
	
	private static $map_between_objects = array
	(
		"startTime",
		"endTime",
		"content",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function fromObject($source_object)
	{
		/* @var $source_object CaptionAssetItem */
		
		$ret = parent::fromObject($source_object);
		
		$this->asset = new KalturaCaptionAsset();
		$this->asset->fromObject($source_object->getAsset());
		
		$entry = $source_object->getEntry();
		
		if ($entry)
		{
			$this->entry = KalturaEntryFactory::getInstanceByType($entry->getType());
			$this->entry->fromObject($entry);
		}
			
		return $ret;
	}
}