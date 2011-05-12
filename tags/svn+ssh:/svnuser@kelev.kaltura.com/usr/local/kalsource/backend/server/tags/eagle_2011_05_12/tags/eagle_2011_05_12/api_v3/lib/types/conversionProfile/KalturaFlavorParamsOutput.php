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
}