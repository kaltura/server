<?php
/**
 * @package plugins.markdown
 * @subpackage api.enum
 */
class KalturaMarkdownProviderType extends KalturaDynamicEnum implements MarkdownProviderType
{
	public static function getEnumClass()
	{
		return 'MarkdownProviderType';
	}
}
