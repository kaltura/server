<?php
/**
 * @package plugins.MicrosoftTeamsDropFolder
 * @subpackage lib
 */

class MicrosoftTeamsDropFolderType implements IKalturaPluginEnum, DropFolderType
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
			MicrosoftTeamsDropFolderPlugin::getApiValue(self::MS_TEAMS) => 'Microsoft Teams Drop Folder Type',
		);
	}
}