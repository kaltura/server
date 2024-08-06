<?php

/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
class KalturaClipsVendorTaskData extends KalturaVendorTaskData
{
	/**
	 * Estimated duration of the clips, in seconds.
	 *
	 * @insertonly
	 * @var int
	 */
	public $clipsDuration;

	/**
	 * Instruction describing the moments to capture or the objectives to achieve with the clips.
	 *
	 * @insertonly
	 * @var string
	 */
	public $instruction;

	/**
	 * List of clips as JSON string.
	 * For example: [{"title": "Title of the first clip", "description": "Description of the first clip", "tags": "Tagged-Example", "start": 127, "duration": 30}]
	 *
	 * @var string
	 */
	public $clipsOutputJson;

	private static $map_between_objects = array
	(
		'clipsDuration',
		'instruction',
		'clipsOutputJson',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject) {
			$dbObject = new kClipsVendorTaskData();
		}

		return parent::toObject($dbObject, $propsToSkip);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull("clipsDuration");
	}
}
