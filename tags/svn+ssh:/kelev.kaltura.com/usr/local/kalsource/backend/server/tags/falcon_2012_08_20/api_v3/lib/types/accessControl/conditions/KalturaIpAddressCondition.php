<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaIpAddressCondition extends KalturaMatchCondition
{
	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = ConditionType::IP_ADDRESS;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kIpAddressCondition();
			
		return parent::toObject($dbObject, $skip);
	}
}
