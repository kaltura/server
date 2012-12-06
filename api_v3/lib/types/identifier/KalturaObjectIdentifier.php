<?php
/**
 * Configuration for extended item in the Kaltura MRSS feeds
 *
 * @package api
 * @subpackage objects
 */
class KalturaObjectIdentifier extends KalturaObject
{
	/**
	 * Identifier of the object
	 * @var KalturaObjectIdentifierField
	 */
	public $identifier;
	
	/**
	 * Comma separated string of enum values denoting which features of the item need to be included in the MRSS 
	 * @dynamicType KalturaObjectFeatureType
	 * @var string
	 */
	public $extendedFeatures;
	
	
	private static $map_between_objects = array(
			"identifier",
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