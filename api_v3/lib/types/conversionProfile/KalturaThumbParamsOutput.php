<?php
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
	public $readyBehavior;
	
	private static $map_between_objects = array
	(
		"thumbParamsId",
		"thumbParamsVersion",
		"thumbAssetId",
		"thumbAssetVersion",
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