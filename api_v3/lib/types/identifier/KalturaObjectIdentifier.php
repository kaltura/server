<?php
/**
 * Configuration for extended item in the Kaltura MRSS feeds
 *
 * @package api
 * @subpackage objects
 */
abstract class KalturaObjectIdentifier extends KalturaObject
{
	/**
	 * Comma separated string of enum values denoting which features of the item need to be included in the MRSS 
	 * @dynamicType KalturaObjectFeatureType
	 * @var string
	 */
	public $extendedFeatures;
	
	
	private static $map_between_objects = array(
		"extendedFeatures",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

}