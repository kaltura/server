<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaThumbParamsOutput extends KalturaThumbParams
{
	/**
	 * @var int
	 * @filter eq
	 */
	public $thumbParamsId;
	
	/**
	 * @var string
	 * @filter eq
	 */
	public $thumbParamsVersion;
	
	/**
	 * @var string
	 * @filter eq
	 */
	public $thumbAssetId;
	
	/**
	 * @var string
	 * @filter eq
	 */
	public $thumbAssetVersion;
	/**
	 * @var int
	 */
	public $rotate;
	
	private static $map_between_objects = array
	(
		"thumbParamsId" => "flavorParamsId",
		"thumbParamsVersion" => "flavorParamsVersion",
		"thumbAssetId" => "flavorAssetId",
		"thumbAssetVersion" => "flavorAssetVersion",
		"rotate" => "rotate",
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
			$object = new thumbParamsOutput();
			
		return parent::toObject($object, $skip);
	}
}