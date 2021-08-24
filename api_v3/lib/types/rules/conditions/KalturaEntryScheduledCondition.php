<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaEntryScheduledCondition extends KalturaCondition
{
	public function __construct()
	{
		$this->type = ConditionType::ENTRY_SCHEDULED;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
		{
			$dbObject = new kEntryScheduledCondition();
		}
		return parent::toObject($dbObject, $skip);
	}
}
