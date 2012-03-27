<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAccessControlProfile extends KalturaObject implements IFilterable 
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
	 * Creation time as Unix timestamp (In seconds) 
	 * 
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * Update time as Unix timestamp (In seconds) 
	 * 
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	/**
	 * True if this access control profile is the partner default
	 *  
	 * @var KalturaNullableBoolean
	 */
	public $isDefault;
	
	/**
	 * Array of access control rules
	 * 
	 * @var KalturaRuleArray
	 */
	public $rules;
	
	private static $mapBetweenObjects = array
	(
		"id",
		"name",
		"systemName",
		"partnerId",
		"description",
		"createdAt",
		"updatedAt",
		"isDefault",
		"rules" => "rulesArray",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	public function validate()
	{
		$this->validatePropertyMaxLength('systemName', 128, true);
		$this->validatePropertyMaxLength('description', 1024, true);
	}
	
	public function validateForInsert($skip = array())
	{
		$this->validatePropertyMinMaxLength('name', 1, 128);
		$this->validate();
		return parent::validateForInsert($skip);
	}
	
	public function validateForUpdate($skip = array())
	{
		$this->validatePropertyMinMaxLength('name', 1, 128, true);
		$this->validate();
		return parent::validateForUpdate($skip);
	}
	
	public function toObject($dbAccessControlProfile = null, $skip = array())
	{
		if(!$dbAccessControlProfile)
			$dbAccessControlProfile = new accessControl();
			
		return parent::toObject($dbAccessControlProfile, $skip);
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