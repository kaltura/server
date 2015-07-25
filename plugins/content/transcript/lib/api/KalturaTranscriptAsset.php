<?php
/**
 * @package plugins.transcript
 * @subpackage api.objects
 */
class KalturaTranscriptAsset extends KalturaAttachmentAsset 
{
	/**
	 * The accuracy of the transcript
	 * @var float
	 */
	public $accuracy;

	/**
	 * Was verified by human or machine
	 * @var bool
	 */
	public $humanVerified;

	/**
	 * The language of the attachment
	 * @var KalturaLanguage
	 */
	public $language;

	/**
	 * The Transcript format
	 * @var KalturaTranscriptType 
	 */
	public $format;

	private static $map_between_objects = array
	(
		"accuracy",
		"humanVerified",
		"language",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$className = TranscriptPlugin::getObjectClass('asset', TranscriptPlugin::getAssetTypeCoreValue(TranscriptAssetType::TRANSCRIPT));
			$object_to_fill = new $className();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
