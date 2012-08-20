<?php
/**
 * @package plugins.remoteMediaInfo
 * @subpackage lib
 */
class RemoteMediaInfoMediaParserType implements IKalturaPluginEnum, mediaParserType
{
	const REMOTE_MEDIAINFO = 'RemoteMediaInfo';
	
	public static function getAdditionalValues()
	{
		return array(
			'REMOTE_MEDIAINFO' => self::REMOTE_MEDIAINFO
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}
