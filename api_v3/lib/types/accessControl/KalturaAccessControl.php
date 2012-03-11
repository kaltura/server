<?php
/**
 * @package api
 * @subpackage objects
 * @deprecated use KalturaAccessControlProfile instead
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
	
	/**
	 * Indicates that the access control profile is new and should be handled using KalturaAccessControlProfile object and accessControlProfile service
	 * 
	 * @var bool
	 * @readonly
	 */
	public $containsUnsuportedRestrictions;
	
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
		if(!$dbObject)
			$dbObject = new accessControl();
			
		/* @var $dbObject accessControl */
		parent::toObject($dbObject);
		
		if ($this->restrictions instanceof KalturaRestrictionArray)
		{
			$rules = array();
			foreach($this->restrictions as $restriction)
			{
				/* @var $restriction KalturaBaseRestriction */
				$rule = $restriction->toRule();
				if($rule)
					$rules[] = $rule;
			}
				
			$dbObject->setRulesArray($rules);
		}
		
		return $dbObject;
	}

	public function toUpdatableObject($dbObject, $skip = array())
	{
		/* @var $dbObject accessControl */
		$rules = $dbObject->getRules();
		foreach($rules as $rule)
			if(!($rule instanceof kAccessControlRestriction))
				throw new KalturaAPIException(KalturaErrors::ACCESS_CONTROL_NEW_VERSION_UPDATE, $dbObject->getId());
		
		parent::toUpdatableObject($dbObject, $skip);
	}
	
	public function fromObject($dbObject)
	{
		parent::fromObject($dbObject);
		
		if (!($dbObject instanceof accessControl))
			return;
			
		$rules = $dbObject->getRulesArray();
		foreach($rules as $rule)
		{
			if(!($rule instanceof kAccessControlRestriction))
			{
				KalturaLog::info("Access control [" . $dbObject->getId() . "] rules are new and cannot be loaded using old object");
				$this->containsUnsuportedRestrictions = true;
				return;
			}
		}
		$this->restrictions = KalturaRestrictionArray::fromDbArray($rules);
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