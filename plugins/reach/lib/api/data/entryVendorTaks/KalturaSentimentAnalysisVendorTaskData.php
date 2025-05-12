<?php

use data\kSentimentAnalysisVendorTaskData;

/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
class KalturaSentimentAnalysisVendorTaskData extends KalturaVendorTaskData
{
	/**
	 * Language code
	 *
	 * @var KalturaLanguageCode
	 */
	public $language;

	/**
	 * JSON string containing the summary output.
	 *
	 * @var string
	 */
	public $sentimentAnalysisOutputJson;


	private static $map_between_objects = array(
		'language',
		'sentimentAnalysisOutputJson',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kSentimentAnalysisVendorTaskData();
		}

		return parent::toObject($dbObject, $propsToSkip);
	}
}
