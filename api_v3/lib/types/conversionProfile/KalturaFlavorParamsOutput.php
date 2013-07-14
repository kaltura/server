<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFlavorParamsOutput extends KalturaFlavorParams
{
	/**
	 * @var int
	 * @filter eq
	 */
	public $flavorParamsId;
	
	/**
	 * @var string
	 */
	public $commandLinesStr;
		
	
	/**
	 * @var string
	 * @filter eq
	 */
	public $flavorParamsVersion;
	
	/**
	 * @var string
	 * @filter eq
	 */
	public $flavorAssetId;
	
	/**
	 * @var string
	 * @filter eq
	 */
	public $flavorAssetVersion;
	
	/**
	 * @var int
	 */
	public $readyBehavior;
	
	private static $map_between_objects = array
	(
		"flavorParamsId",
		"commandLinesStr",
		"flavorParamsVersion",
		"flavorAssetId",
		"flavorAssetVersion",
		"readyBehavior",
	);
	
	// attributes that defined in flavorParams and not in flavorParamsOutput
	private static $skip_attributes = array
	(
		"systemName",
	);
	
	public function getMapBetweenObjects()
	{
		$map = array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
		foreach(self::$skip_attributes as $skip_attribute)
		{
			if(isset($map[$skip_attribute]))
				unset($map[$skip_attribute]);
				
			$key = array_search($skip_attribute, $map);
			if($key !== false)
				unset($map[$key]);
		}
		return $map;
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
			$object = new flavorParamsOutput();
			
		return parent::toObject($object, $skip);
	}
}