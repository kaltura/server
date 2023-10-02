<?php
/**
 * @package api
 * @subpackage objects
 * @relatedService UserAppRoleService
 */

class KalturaAppRole extends KalturaObject implements IRelatedFilterable
{
	/**
	 * @var int
	 */
	protected $kuserId;
	
	/**
	 * @var string
	 * @insertonly
	 */
	public $appGuid;
	
	/**
	 * @var int
	 */
	public $userRoleId;
	
	/**
	 * Entry creation date as Unix timestamp (In seconds)
	 *
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * Entry updated date as Unix timestamp (In seconds)
	 *
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	private static $map_between_objects = array
	(
		"appGuid",
		"userRoleId",
		"createdAt",
		"updatedAt"
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/**
	 * @return array
	 */
	function getExtraFilters()
	{
		return array();
	}
	
	/**
	 * @return array
	 */
	function getFilterDocs()
	{
		return array();
	}
}

