<?php
/**
 * Represents the current request http headers context
 *
 * @package api
 * @subpackage objects
 */

class KalturaHttpHeaderContextField extends KalturaStringField
{
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kHttpHeaderContextField();

		return parent::toObject($dbObject, $skip);
	}

}