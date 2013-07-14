<?php
/**
 * Represents the current request IP address context 
 * 
 * @package api
 * @subpackage objects
 */
class KalturaIpAddressContextField extends KalturaStringField
{
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kIpAddressContextField();
			
		return parent::toObject($dbObject, $skip);
	}
}