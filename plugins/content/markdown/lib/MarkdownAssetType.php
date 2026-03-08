<?php
/**
 * @package plugins.markdown
 * @subpackage lib.enum
 */
class MarkdownAssetType implements IKalturaPluginEnum, assetType
{
	const MARKDOWN = 'Markdown';
	
	public static function getAdditionalValues()
	{
		return array(
			'MARKDOWN' => self::MARKDOWN,
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
