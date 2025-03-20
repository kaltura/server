<?php
/**
 * @package plugins.rsvp
 * @subpackage model
 */

class RsvpUserEntry extends UserEntry
{
	const RSVP_OM_CLASS = 'RsvpUserEntry';

	public function __construct()
	{
		$this->setType(RsvpPlugin::getRsvpUserEntryTypeCoreValue(RsvpUserEntryType::RSVP));
		parent::__construct();
	}
}
