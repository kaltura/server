<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAuthenticationTypesCondition extends KalturaCondition
{
	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = ConditionType::VALIDATE_AUTHENTICATION_TYPES;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kAuthenticationTypesCondition();
			
		return parent::toObject($dbObject, $skip);
	}
}
