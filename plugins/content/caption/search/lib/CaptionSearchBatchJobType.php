<?php
/**
 * @package plugins.captionSearch
 * @subpackage api.enum
 */
class CaptionSearchBatchJobType implements IKalturaPluginEnum, BatchJobType
{
	const PARSE_CAPTION_ASSET = 'parseCaptionAsset';
	
	public static function getAdditionalValues()
	{
		return array(
			'PARSE_CAPTION_ASSET' => self::PARSE_CAPTION_ASSET
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
