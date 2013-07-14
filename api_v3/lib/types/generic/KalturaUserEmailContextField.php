<?php
/**
 * Represents the current session user e-mail address context
 * 
 * @package api
 * @subpackage objects
 */
class KalturaUserEmailContextField extends KalturaStringField
{
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kUserEmailContextField();
			
		return parent::toObject($dbObject, $skip);
	}
}