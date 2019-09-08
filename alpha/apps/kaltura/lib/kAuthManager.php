<?php

class kAuthManager implements kObjectChangedEventConsumer
{

	const TWO_FACTOR_FIELD = 'useTwoFactorAuthentication';
	const SSO_FIELD = 'useSso';
	static $handleObjectChanged = true;

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if( self::$handleObjectChanged && $object instanceof Partner &&
			in_array(PartnerPeer::CUSTOM_DATA, $modifiedColumns) &&
			$object->isCustomDataModified(self::TWO_FACTOR_FIELD) &&
			$object->getUseTwoFactorAuthentication())
		{
			$oldCustomDataValues = $object->getCustomDataOldValues();
			$old2FAValue = $oldCustomDataValues[''][self::TWO_FACTOR_FIELD];
			if ($old2FAValue != $object->getUseTwoFactorAuthentication())
			{
				self::$handleObjectChanged = false;
				return true;
			}
		}
		if( self::$handleObjectChanged && $object instanceof Partner &&
			in_array(PartnerPeer::CUSTOM_DATA, $modifiedColumns) &&
			$object->isCustomDataModified(self::SSO_FIELD) &&
			$object->getUseSso())
		{
			$oldCustomDataValues = $object->getCustomDataOldValues();
			$oldSsoValue = $oldCustomDataValues[''][self::SSO_FIELD];
			if ($oldSsoValue != $object->getUseSso())
			{
				self::$handleObjectChanged = false;
				return true;
			}
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
			if ($object->getUseSso() && !$object->getUseTwoFactorAuthentication())
			{
				$job = authenticationUtils::addSsoMailJob($object, $adminKuser, kuserPeer::KALTURA_EXISTING_USER_ENABLE_SSO_EMAIL);
				if(!$job)
				{
					KalturaLog::warning('Mail Job was not added');
				}
			}
			else
			{
				$userLoginData = $adminKuser->getLoginData();
				if(!$userLoginData->getSeedFor2FactorAuth())
				{
					authenticationUtils::generateNewSeed($userLoginData);
				}
				$job = authenticationUtils::addAuthMailJob($object, $adminKuser, kuserPeer::KALTURA_EXISTING_USER_ENABLE_2FA_EMAIL);
				if(!$job)
				{
					KalturaLog::warning('Missing QR URL, Mail Job was not added');
				}
			}
		}
	}

}