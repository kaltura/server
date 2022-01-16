<?php

/**
 * @package plugins.leaderboard
 */
class LeaderboardPlugin extends KalturaPlugin implements IKalturaServices
{
	const PLUGIN_NAME = 'leaderboard';
	
	/* (non-PHPdoc)
	* @see IKalturaServices::getServicesMap()
	*/
	public static function getServicesMap()
	{
		$map = array(
			'userScore' => 'UserScoreService',
		);
		return $map;
	}
	
	/* (non-PHPdoc)
     * @see IKalturaPlugin::getPluginName()
     */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/**
	 * @param $valueName
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}