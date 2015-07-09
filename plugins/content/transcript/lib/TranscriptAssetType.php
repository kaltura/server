<?php
/**
 * @package plugins.transcript
 * @subpackage lib.enum
 */
class TranscriptAssetType extends AttachmentAssetType
{
	const TRANSCRIPT = 'Transcript';
	
	public static function getAdditionalValues()
	{
		return array(
			'TRANSCRIPT' => self::TRANSCRIPT,
		);
	}
}
