<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaCaptionsEntryVendorTaskJobData extends KalturaEntryVendorTaskJobData
{
	/**
	 * @var KalturaCatalogItemLanguage
	 */
	public $sourceLanguage;
	
	/**
	 * @var KalturaVendorCatalogItemOutputFormat
	 */
	public $outputFormat;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableSpeakerId;
	
	
	private static $map_between_objects = array (
		'sourceLanguages',
		'outputFormats',
		'enableSpeakerId',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
 */
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kCaptionsEntryVendorTaskJobData();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
}