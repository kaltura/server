<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAccessControlBlockAction extends KalturaRuleAction
{
	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = RuleActionType::BLOCK;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kRuleAction(RuleActionType::BLOCK);
			
		return parent::toObject($dbObject, $skip);
	}
}