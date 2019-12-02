<?php
/**
 * @package plugins.registration
 * @subpackage model.enum
 */
class RegistrationUserEntryType implements IKalturaPluginEnum, UserEntryType
{
	const REGISTRATION = 'REGISTRATION';

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array(
			'REGISTRATION' => self::REGISTRATION,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			self::REGISTRATION => 'Registration User Entry Type',
		);
	}
}