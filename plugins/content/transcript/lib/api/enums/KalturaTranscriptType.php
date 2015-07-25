<?php
/**
 * @package plugins.transcript
 * @subpackage api.enum
 */
class KalturaTranscriptType extends KalturaDynamicEnum implements TranscriptType
{
	public static function getEnumClass()
	{
		return 'TranscriptType';
	}
}
