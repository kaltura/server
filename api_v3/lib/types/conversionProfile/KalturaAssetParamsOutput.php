<?php
class KalturaAssetParamsOutput extends KalturaAssetParams
{
	/**
	 * @var int
	 * @filter eq
	 */
	public $assetParamsId;
	
	/**
	 * @var string
	 * @filter eq
	 */
	public $assetParamsVersion;
	
	/**
	 * @var string
	 * @filter eq
	 */
	public $assetId;
	
	/**
	 * @var string
	 * @filter eq
	 */
	public $assetVersion;
	
	/**
	 * @var int
	 */
	public $readyBehavior;
	
	private static $map_between_objects = array
	(
		"assetParamsId",
		"assetParamsVersion",
		"assetId",
		"assetVersion",
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
