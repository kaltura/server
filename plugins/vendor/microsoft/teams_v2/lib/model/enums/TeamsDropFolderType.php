<?php
/**
 * @package plugins.KTeams
 * @subpackage lib
 */

class TeamsDropFolderType implements IKalturaPluginEnum, DropFolderType
{
	const K_TEAMS = 'K_TEAMS';

	/**
	 * @inheritDoc
	 */
	public static function getAdditionalValues()
	{
		return array(
			'K_TEAMS' => self::K_TEAMS,
		);
	}

	/**
	 * @inheritDoc
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			KTeamsPlugin::getApiValue(self::K_TEAMS) => 'K Teams Drop Folder Type',
		);
	}
}