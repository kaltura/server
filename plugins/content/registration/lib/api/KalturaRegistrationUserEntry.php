<?php
/**
 * @package plugins.registration
 * @subpackage api
 */
class KalturaRegistrationUserEntry extends KalturaUserEntry
{
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if(is_null($dbObject))
		{
			$dbObject = new RegistrationUserEntry();
		}

		return parent::toObject($dbObject, $propertiesToSkip);
	}
}