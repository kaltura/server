<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaStorageAddAction extends KalturaRuleAction
{
	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = RuleActionType::ADD_TO_STORAGE;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kRuleAction(RuleActionType::ADD_TO_STORAGE);
			
		return parent::toObject($dbObject, $skip);
	}
}