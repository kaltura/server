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

	private static $map_between_objects = array
	(
		"accuracy",
		"humanVerified",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
