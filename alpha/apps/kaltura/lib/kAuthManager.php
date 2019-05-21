<?php

class kAuthManager implements kObjectChangedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if( $object instanceof Partner &&
			in_array(partnerPeer::CUSTOM_DATA, $modifiedColumns) &&
			$object->isCustomDataModified('useTwoFactorAuthentication') &&
			$object->getUseTwoFactorAuthentication())
		{
			return true;
		}

		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		$adminKusers = Partner::getAdminLoginUsersList($object->getId());
		foreach ($adminKusers as $adminKuser)
		{
			$userLoginData = $adminKuser->getLoginData();
			if(!$userLoginData->getSeedFor2FactorAuth())
			{
				authenticationUtils::generateNewSeed($adminKuser);
			}
			$job = authenticationUtils::add2FAMailJob($adminKuser);
			if(!$job)
			{
				KalturaLog::warning('Missing QR URL, Mail Job was not added');
			}
		}
	}

}