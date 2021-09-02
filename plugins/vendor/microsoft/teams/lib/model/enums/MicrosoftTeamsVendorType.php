<?php
/**
 * @package plugins.MicrosoftTeamsDropFolder
 * @subpackage lib
 */

class MicrosoftTeamsVendorType implements IKalturaPluginEnum, VendorTypeEnum
{
	const MS_TEAMS = 'MS_TEAMS';

	/**
	 * @inheritDoc
	 */
	public static function getAdditionalValues()
	{
		return array(
			'MS_TEAMS' => self::MS_TEAMS,
		);
	}

	/**
	 * @inheritDoc
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			MicrosoftTeamsDropFolderPlugin::getApiValue(self::MS_TEAMS) => 'Microsoft Teams Vendor Type',
		);
	}
}