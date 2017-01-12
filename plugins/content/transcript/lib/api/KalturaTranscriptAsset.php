<?php
/**
 * @package plugins.transcript
 * @subpackage api.objects
 */
class KalturaTranscriptAsset extends KalturaAttachmentAsset
{
	/**
	 * The accuracy of the transcript - values between 0 and 1
	 * @var float
	 */
	public $accuracy;
	
	/**
	 * Was verified by human or machine
	 * @var KalturaNullableBoolean
	 */
	public $humanVerified;
	
	/**
	 * The language of the transcript
	 * @var KalturaLanguage
	 */
	public $language;
	
	/**
	 * The provider of the transcript
	 * @var KalturaTranscriptProviderType
	 */
	public $providerType;
	
	private static $map_between_objects = array
	(
		"accuracy",
		"humanVerified",
		"language",
		"providerType",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new TranscriptAsset();
	
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
