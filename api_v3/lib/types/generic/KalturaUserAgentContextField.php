<?php
/**
 * Represents the current request user agent context
 * 
 * @package api
 * @subpackage objects
 */
class KalturaUserAgentContextField extends KalturaStringField
{
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kUserAgentContextField();
			
		return parent::toObject($dbObject, $skip);
	}
}