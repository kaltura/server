<?php
/**
 * @package plugins.entryPermissionLevel
 * @subpackage model.enum
 */
class PermissionLevelUserEntryType implements IKalturaPluginEnum, UserEntryType
{
	const PERMISSION_LEVEL = 'PERMISSION_LEVEL';

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array(
			'PERMISSION_LEVEL' => self::PERMISSION_LEVEL,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			self::PERMISSION_LEVEL => 'Entry permission level User Entry Type',
		);
	}
}