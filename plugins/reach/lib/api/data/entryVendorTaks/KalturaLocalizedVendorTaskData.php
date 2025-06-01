<?php

/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
class KalturaLocalizedVendorTaskData extends KalturaVendorTaskData
{
	/**
	 * Language code
	 *
	 * @var KalturaLanguageCode
	 */
	public $language;

	private static $map_between_objects = array(
		'language',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kLanguageVendorTaskData();
		}

		return parent::toObject($dbObject, $propsToSkip);
	}
}
