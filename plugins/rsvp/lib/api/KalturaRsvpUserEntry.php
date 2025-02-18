<?php
/**
 * @package plugins.rsvp
 * @subpackage api
 * @relatedService UserEntryService
 */

class KalturaRsvpUserEntry extends KalturaUserEntry
{
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if (is_null($dbObject))
		{
			$dbObject = new RsvpUserEntry();
		}

		return parent::toObject($dbObject, $propertiesToSkip);
	}
}
