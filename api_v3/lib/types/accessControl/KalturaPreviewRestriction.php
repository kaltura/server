<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPreviewRestriction extends KalturaSessionRestriction 
{
	/**
	 * The preview restriction length 
	 * 
	 * @var int
	 */
	public $previewLength;
	
	private static $mapBetweenObjects = array
	(
		"previewLength",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}