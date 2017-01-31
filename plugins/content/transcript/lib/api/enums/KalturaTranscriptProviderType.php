<?php
/**
 * @package plugins.transcript
 * @subpackage api.enum
 */
class KalturaTranscriptProviderType extends KalturaDynamicEnum implements TranscriptProviderType
{
	public static function getEnumClass()
	{
		return 'TranscriptProviderType';
	}
}