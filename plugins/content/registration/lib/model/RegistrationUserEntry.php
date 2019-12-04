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

	public function postUpdate(PropelPDO $con = null)
	{
		if($this->isColumnModified(UserEntryPeer::STATUS) && $this->getStatus() == UserEntryStatus::DELETED)
		{
			kEventsManager::raiseEventDeferred(new kObjectDeletedEvent($this));
		}

		parent::postUpdate($con);
	}
}