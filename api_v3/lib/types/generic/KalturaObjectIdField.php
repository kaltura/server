<?php
/**
 * Evaluates PHP statement, depends on the execution context
 * 
 * @package api
 * @subpackage objects
 */
class KalturaObjectIdField extends KalturaStringField
{
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kObjectIdStringField();
			
		return parent::toObject($dbObject, $skip);
	}
}