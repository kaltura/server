<?php
/**
 * @package plugins.attachment
 * @subpackage api.objects
 * @relatedService AttachmentAssetService
 */
abstract class KalturaTextualAttachmentAsset extends KalturaAttachmentAsset
{
	/**
	 * The language of the transcript
	 * @var KalturaLanguage
	 */
	public $language;
	
	/**
	 * Was verified by human or machine
	 * @var KalturaNullableBoolean
	 */
	public $humanVerified;
	
	private static $map_between_objects = array
	(
		"humanVerified",
		"language",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
