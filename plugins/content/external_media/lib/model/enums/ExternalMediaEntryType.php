<?php
/**
 * @package plugins.externalMedia
 * @subpackage model.enum
 */
class ExternalMediaEntryType implements IKalturaPluginEnum, entryType
{
	const EXTERNAL_MEDIA = 'externalMedia';
	
	public static function getAdditionalValues()
	{
		return array(
			'EXTERNAL_MEDIA' => self::EXTERNAL_MEDIA,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			ExternalMediaPlugin::getApiValue(self::EXTERNAL_MEDIA) => 'External Media',
		);
	}
}
