<?php

/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
class KalturaSummaryVendorTaskData extends KalturaLocalizedVendorTaskData
{
	/**
	 * Type of summary.
	 *
	 * @var KalturaTypeOfSummaryTaskData
	 */
	public $typeOfSummary;

	/**
	 * Writing style of the summary.
	 *
	 * @var KalturaSummaryWritingStyleTaskData
	 */
	public $writingStyle;

	/**
	 * JSON string containing the summary output.
	 *
	 * @var string
	 * @deprecated Please use outputJson instead.
	 */
	public $summaryOutputJson;

	private static $map_between_objects = array(
		'typeOfSummary',
		'writingStyle',
		'summaryOutputJson',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kSummaryVendorTaskData();
		}

		return parent::toObject($dbObject, $propsToSkip);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull("typeOfSummary");
		$this->validatePropertyNotNull("writingStyle");
	}
}
