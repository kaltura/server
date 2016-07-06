<?php
/**
 * @package plugins.transcript
 * @subpackage lib.enum
 */
class TranscriptAssetType implements IKalturaPluginEnum, assetType
{
	const TRANSCRIPT = 'Transcript';
	
	public static function getAdditionalValues()
	{
		return array(
			'TRANSCRIPT' => self::TRANSCRIPT,
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
