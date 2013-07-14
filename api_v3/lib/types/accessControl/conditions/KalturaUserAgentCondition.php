<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUserAgentCondition extends KalturaRegexCondition
{
	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = ConditionType::USER_AGENT;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kUserAgentCondition();
			
		return parent::toObject($dbObject, $skip);
	}
}
