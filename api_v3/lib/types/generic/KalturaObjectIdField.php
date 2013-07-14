<?php
/**
 * Evaluates object ID according to given context
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
			$dbObject = new kObjectIdField();
			
		return parent::toObject($dbObject, $skip);
	}
}