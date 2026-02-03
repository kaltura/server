<?php

/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
class KalturaSpeechToVideoVendorTaskData extends KalturaVendorTaskData
{
	/**
	 * The identifier of the avatar to be used for generating the video
	 *
	 * @var string
	 */
	public $avatarId;

	/**
	 * Optional. Conversion profile to be used for the generated video media entry
	 *
	 * @var int
	 */
	public $conversionProfileId;

	private static $map_between_objects = array
	(
		'avatarId',
		'conversionProfileId',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject) {
			$dbObject = new kSpeechToVideoVendorTaskData();
		}

		return parent::toObject($dbObject, $propsToSkip);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		parent::validateForInsert($propertiesToSkip);
		parent::validatePropertyNotNull('avatarId');
	}
}
