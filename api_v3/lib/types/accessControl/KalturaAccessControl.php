<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAccessControl extends KalturaObject implements IFilterable 
{
	/**
	 * The id of the Access Control Profile
	 * 
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $id;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * The name of the Access Control Profile
	 * 
	 * @var string
	 */
	public $name;
	
	/**
	 * System name of the Access Control Profile
	 * 
	 * @var string
	 * @filter eq,in
	 */
	public $systemName;
	
	/**
	 * The description of the Access Control Profile
	 * 
	 * @var string
	 */
	public $description;
	
	/**
	 * Creation date as Unix timestamp (In seconds) 
	 * 
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * True if this Conversion Profile is the default
	 *  
	 * @var KalturaNullableBoolean
	 */
	public $isDefault;
	
	/**
	 * Array of Access Control Restrictions
	 * 
	 * @var KalturaRestrictionArray
	 */
	public $restrictions;
	
	private static $mapBetweenObjects = array
	(
		"id",
		"name",
		"systemName",
		"partnerId",
		"description",
		"createdAt",
		"isDefault",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		parent::toObject($dbObject);
		
		if ($this->restrictions instanceof KalturaRestrictionArray)
		{
			foreach($this->restrictions as $restriction)
			{
				$dbRestriction = KalturaRestrictionFactory::getDbInstanceApiObject($restriction);
				$restriction->toObject($dbRestriction); 
				$dbObject->setRestriction($dbRestriction);
			}
		}
	}

	public function toUpdatableObject($dbObject, $skip = array())
	{
		parent::toUpdatableObject($dbObject, $skip);
		
		if ($this->restrictions !== null && $this->restrictions instanceof KalturaRestrictionArray)
		{
			$dbObject->clearRestrictions();
			foreach($this->restrictions as $restriction)
			{
				$dbRestriction = KalturaRestrictionFactory::getDbInstanceApiObject($restriction);
				$restriction->toObject($dbRestriction); 
				$dbObject->setRestriction($dbRestriction);
			}
		}
	}
	
	public function fromObject($dbObject)
	{
		parent::fromObject($dbObject);
		
		if ($dbObject instanceof accessControl)
		{
			$dbRestrictions = $dbObject->getRestrictions();
			$this->restrictions = KalturaRestrictionArray::fromDbArray($dbRestrictions);
		}
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