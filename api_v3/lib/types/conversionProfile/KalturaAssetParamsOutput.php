<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAssetParamsOutput extends KalturaAssetParams
{
	/**
	 * @var int
	 */
	public $assetParamsId;
	
	/**
	 * @var string
	 */
	public $assetParamsVersion;
	
	/**
	 * @var string
	 */
	public $assetId;
	
	/**
	 * @var string
	 */
	public $assetVersion;
	
	/**
	 * @var int
	 */
	public $readyBehavior;

	/**
	 * The container format of the Flavor Params
	 *  
	 * @var KalturaContainerFormat
	 */
	public $format;
	
	private static $map_between_objects = array
	(
		"assetParamsId",
		"assetParamsVersion",
		"assetId",
		"assetVersion",
		"readyBehavior",
		"format",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($object = null, $skip = array())
	{
		if(is_null($object))
			$object = new assetParamsOutput();
			
		return parent::toObject($object, $skip);
	}
}
