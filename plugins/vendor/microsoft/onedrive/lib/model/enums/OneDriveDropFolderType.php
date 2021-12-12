<?php
/**
 * @package plugins.OneDrive
 * @subpackage lib
 */

class OneDriveDropFolderType implements IKalturaPluginEnum, DropFolderType
{
	const ONE_DRIVE = 'ONE_DRIVE';

	/**
	 * @inheritDoc
	 */
	public static function getAdditionalValues()
	{
		return array(
			'ONE_DRIVE' => self::ONE_DRIVE,
		);
	}

	/**
	 * @inheritDoc
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			OneDrivePlugin::getApiValue(self::ONE_DRIVE) => 'OneDrive Drop Folder Type',
		);
	}
}