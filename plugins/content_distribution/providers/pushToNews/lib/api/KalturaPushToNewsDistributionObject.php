<?php
/**
 * @package plugins.pushToNewsDistribution
 * @subpackage api.objects
 */
class KalturaPushToNewsDistributionObject extends KalturaObject
{
	/**
	 * @var string
	 */
	public $type;
	
	/**
	 * @var string
	 */
	public $contents;
	
	/**
	 */
	public function __construct()
	{
	}
		
	/**
	 * Maps the object attributes to getters and setters for Core-to-API translation and back
	 *  
	 * @var array
	 */
	private static $map_between_objects = array
	(
		'type',
		'contents',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
