<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaPartnerCatalogItem extends KalturaObject
{
	/**
	 * @var int
	 * @readonly
	 */
	public $id;
	
	/**
	 * @var int
	 */
	public $partnerId;
	
	/**
	 * @var int
	 */
	public $vendorCatalogItemId;
	
	/**
	 * @var KalturaPartnerCatalogItemStatus
	 */
	public $status;
	
	
	private static $map_between_objects = array
	(
		'id',
		'partnerId',
		'vendorCatalogItemId',
		'status',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::getMapBetweenObjects()
	 */
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
