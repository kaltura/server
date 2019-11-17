<?php
/**
 * @package plugins.registration
 * @subpackage model
 */
class RegistrationUserEntry extends UserEntry
{
	const REGISTRATION_OM_CLASS = 'RegistrationUserEntry';

	public function __construct()
	{
		$this->setType(RegistrationPlugin::getRegistrationUserEntryTypeCoreValue(RegistrationUserEntryType::REGISTRATION));
		parent::__construct();
	}
}