<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAssetParams extends KalturaObject implements IFilterable 
{
	/**
	 * The id of the Flavor Params
	 * 
	 * @var int
	 * @readonly
	 */
	public $id;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * The name of the Flavor Params
	 * 
	 * @var string 
	 */
	public $name;
	
	/**
	 * The description of the Flavor Params
	 * 
	 * @var string
	 */
	public $description;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 *  
	 * @var int
	 * @readonly
	 */
	public $createdAt;
	
	/**
	 * True if those Flavor Params are part of system defaults
	 * 
	 * @var KalturaNullableBoolean
	 * @readonly
	 * @filter eq
	 */
	public $isSystemDefault;
	
	/**
	 * The Flavor Params tags are used to identify the flavor for different usage (e.g. web, hd, mobile)
	 * 
	 * @var string
	 * @filter eq
	 */
	public $tags;

	/**
	 * The container format of the Flavor Params
	 *  
	 * @var KalturaContainerFormat
	 * @filter eq
	 */
	public $format;
	
	private static $map_between_objects = array
	(
		"id",
		"partnerId",
		"name",
		"description",
		"createdAt",
		"isSystemDefault" => "isDefault",
		"tags",
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
}
