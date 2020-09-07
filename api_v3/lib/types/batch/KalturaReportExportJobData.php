<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaReportExportJobData extends KalturaJobData
{
	/**
	 * @var string
	 */
	public $recipientEmail;

	/**
	 * @var KalturaReportExportItemArray
	 */
	public $reportItems;

	/**
	 * @var string
	 */
	public $filePaths;

	/**
	 * @var string
	 */
	public $reportsGroup;

	/**
	 * @var KalturaReportExportFileArray
	 */
	public $files;

	public $timeZoneOffset;
	
	public $timeReference;

	private static $map_between_objects = array
	(
		"recipientEmail",
		"reportItems",
		"filePaths",
		"reportsGroup",
		"files",
		"timeZoneOffset",
		"timeReference",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($jobData = null, $props_to_skip = array())
	{
		if (!$jobData)
		{
			$jobData = new kReportExportJobData();
		}

		$jobData->setReportItems($this->reportItems);

		return parent::toObject($jobData, $props_to_skip);
	}

}
