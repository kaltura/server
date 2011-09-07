<?php
/**
 * @package plugins.remoteMediaInfo
 * @subpackage lib
 */
class RemoteMediaInfoMediaParserType implements IKalturaPluginEnum, mediaParserType
{
	const REMOTE_MEDIA_INFO = 'RemoteMediaInfo';
	
	public static function getAdditionalValues()
	{
		return array(
			'REMOTE_MEDIA_INFO' => self::REMOTE_MEDIAINFO
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
