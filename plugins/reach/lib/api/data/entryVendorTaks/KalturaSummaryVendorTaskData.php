<?php

/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
class KalturaSummaryVendorTaskData extends KalturaVendorTaskData
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
	 * Language code
	 *
	 * @var KalturaLanguage
	 */
	public $language;

	/**
	 * JSON string containing the summary output.
	 *
	 * @var string
	 */
	public $summaryOutputJson;

	private static $map_between_objects = array(
		'typeOfSummary',
		'writingStyle',
		'language',
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

	public function getSummaryOutputJson(): ?string
	{
		return $this->summaryOutputJson;
	}

	public function setSummaryOutputJson(?string $summaryOutputJson): void
	{
		$this->summaryOutputJson = $summaryOutputJson;
	}

	public function getTypeOfSummary(): typeOfSummaryTaskData
	{
		return $this->typeOfSummary;
	}

	public function setTypeOfSummary(typeOfSummaryTaskData $typeOfSummary): void
	{
		$this->typeOfSummary = $typeOfSummary;
	}

	public function getWritingStyle(): SummaryWritingStyleTaskData
	{
		return $this->writingStyle;
	}

	public function setWritingStyle(SummaryWritingStyleTaskData $writingStyle): void
	{
		$this->writingStyle = $writingStyle;
	}
}