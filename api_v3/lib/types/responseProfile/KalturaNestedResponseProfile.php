<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaNestedResponseProfile extends KalturaNestedResponseProfileBase
{
	/**
	 * Friendly name
	 * 
	 * @var string
	 */
	public $name;
	
	/**
	 * @var KalturaResponseProfileType
	 */
	public $type;
	
	/**
	 * @var KalturaStringArray
	 */
	public $fields;
	
	/**
	 * @var KalturaFilter
	 */
	public $filter;
	
	/**
	 * @var KalturaFilterPager
	 */
	public $pager;
	
	/**
	 * @var KalturaNestedResponseProfileBaseArray
	 */
	public $relatedProfiles;
	
	private static $map_between_objects = array(
		'name', 
		'type',
		'fields',
		'filter',
		'pager',
		'relatedProfiles',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage($sourceObject, $propertiesToSkip)
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		$this->validatePropertyMinLength('name', 2);
		$this->validatePropertyNotNull('type');
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($object = null, $propertiesToSkip = array())
	{
		if(is_null($object))
		{
			$object = new kResponseProfile();
		}
		parent::toObject($object, $propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaResponseProfileBase::getRelatedProfiles()
	 */
	public function getRelatedProfiles()
	{
		if($this->relatedProfiles)
		{
			return $this->relatedProfiles;
		}
		
		return array();
	}

	/* (non-PHPdoc)
	 * @see KalturaNestedResponseProfileBase::get()
	 */
	public function get()
	{
		return $this;
	}
}