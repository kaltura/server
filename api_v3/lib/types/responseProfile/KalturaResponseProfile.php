<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaResponseProfile extends KalturaResponseProfileBase implements IFilterable
{
	/**
	 * Auto generated numeric identifier
	 * 
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $id;
	
	/**
	 * Friendly name
	 * 
	 * @var string
	 */
	public $name;
	
	/**
	 * Unique system name
	 * 
	 * @var string
	 * @filter eq,in
	 */
	public $systemName;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * Creation time as Unix timestamp (In seconds) 
	 * 
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * Update time as Unix timestamp (In seconds) 
	 * 
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	/**
	 * @var KalturaResponseProfileStatus
	 * @readonly
	 * @filter eq,in
	 */
	public $status;
	
	/**
	 * @var KalturaResponseProfileType
	 */
	public $type;
	
	/**
	 * Comma separated fields list to be included or excluded
	 * 
	 * @var string
	 */
	public $fields;
	
	/**
	 * @var KalturaRelatedFilter
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
	
	/**
	 * @var KalturaResponseProfileMappingArray
	 */
	public $mappings;
	
	public function __construct(ResponseProfile $responseProfile = null)
	{
		if($responseProfile)
		{
			$this->fromObject($responseProfile);
		}
	}
	
	private static $map_between_objects = array(
		'id', 
		'name', 
		'systemName', 
		'partnerId',
		'createdAt',
		'updatedAt',
		'status',
		'type',
		'fields',
		'pager',
		'relatedProfiles',
		'mappings',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage($sourceObject, $propertiesToSkip)
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		$this->validatePropertyMinLength('name', 2);
		$this->validatePropertyMinLength('systemName', 2);
		$this->validatePropertyNotNull('type');
		
		$maxNestingLevel = kConf::get('response_profile_max_nesting_level', 'local', 2);
		$maxPageSize = kConf::get('response_profile_max_page_size', 'local', 100);
		
		$this->validateNestedObjects($maxPageSize, $maxNestingLevel);
	}
	
	protected function validateNestedObjects($maxPageSize, $maxNestingLevel)
	{
		if(!$this->relatedProfiles)
		{
			return;
		}
		
		if($maxNestingLevel > 0)
		{
			foreach($this->relatedProfiles as $relatedProfile)
			{
				/* @var $relatedProfile KalturaResponseProfile */
				$relatedProfile->validateNestedObjects($maxPageSize, $maxNestingLevel - 1);
				
				if($relatedProfile->pager)
				{
					$relatedProfile->pager->validatePropertyMaxValue('pageSize', $maxPageSize, true);
				}
			}
		}
		else
		{
			if(count($this->relatedProfiles))
			{
				throw new KalturaAPIException(KalturaErrors::RESPONSE_PROFILE_MAX_NESTING_LEVEL);
			}
		}
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($object = null, $propertiesToSkip = array())
	{
		if(is_null($object))
		{
			$object = new ResponseProfile();
		}
		
		if($this->filter)
		{
			$object->setFilterApiClassName(get_class($this->filter));
			$object->setFilter($this->filter->toObject());
		}
		
		return parent::toObject($object, $propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject($srcObj, $responseProfile)
	 */
	public function fromObject($srcObj, KalturaResponseProfileBase $responseProfile = null)
	{
		/* @var $srcObj ResponseProfile */
		parent::fromObject($srcObj, $responseProfile);
		
		if($srcObj->getFilter() && $this->shouldGet('filter', $responseProfile))
		{
			$filterApiClassName = $srcObj->getFilterApiClassName();
			$this->filter = new $filterApiClassName();
			$this->filter->fromObject($srcObj->getFilter());
		}
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
}