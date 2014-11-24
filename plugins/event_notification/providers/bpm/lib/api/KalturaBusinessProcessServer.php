<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage api.objects
 */
abstract class KalturaBusinessProcessServer extends KalturaObject implements IFilterable
{	
	/**
	 * Auto generated identifier
	 * @var int
	 * @readonly
	 * @filter eq,in,notin
	 */
	public $id;

	/**
	 * Server creation date as Unix timestamp (In seconds)
	 * 
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;

	/**
	 * Server update date as Unix timestamp (In seconds)
	 * 
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;

	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $partnerId;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $systemName;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var KalturaBusinessProcessServerStatus
	 * @readonly
	 * @filter eq,not,in,notin
	 */
	public $status;

	/**
	 * The type of the server, this is auto filled by the derived server object
	 * @var KalturaBusinessProcessProvider
	 * @readonly
	 * @filter eq,in
	 */
	public $type;
	
	/**
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'id',
		'createdAt',
		'updatedAt',
		'partnerId',
		'name',
		'systemName',
		'description',
		'status',
		'type',
	);
		 
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getExtraFilters()
	 */
	public function getExtraFilters()
	{
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getFilterDocs()
	 */
	public function getFilterDocs()
	{
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('type');
		$propertiesToSkip[] = 'type';
		return parent::validateForInsert($propertiesToSkip);
	}
		
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$propertiesToSkip[] = 'type';
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	/**
	 * @param int $type core enum value of BusinessProcessProvider
	 * @return KalturaBusinessProcessServer
	 */
	public static function getInstanceByType($type)
	{
		return KalturaPluginManager::loadObject('KalturaBusinessProcessServer', $type);
	}
}