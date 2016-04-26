<?php
/**
 * @package plugins.shortLink
 * @subpackage api.objects
 */
class KalturaShortLink extends KalturaObject implements IFilterable
{
	/**
	 * @var string
	 * @readonly
	 * @filter eq,in
	 */
	public $id;

	/**
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;

	/**
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;

	/**
	 * @var int
	 * @filter gte,lte,order
	 */
	public $expiresAt;

	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $partnerId;

	/**
	 * @var string
	 * @filter eq,in
	 */
	public $userId;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 * @filter eq,in
	 */
	public $systemName;

	/**
	 * @var string
	 */
	public $fullUrl;

	/**
	 * @var KalturaShortLinkStatus
	 * @filter eq,in
	 */
	public $status;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'id',
		'createdAt',
		'updatedAt',
		'partnerId',
		'userId' => 'puserId',
		'name',
		'systemName',
		'fullUrl',
		'status',
		'expiresAt',
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