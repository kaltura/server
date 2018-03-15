<?php

/**
 * @package plugins.scheduledTask
 * @subpackage api.objects
 */
class KalturaScheduledTaskJobData extends KalturaJobData
{
	/**
	 * @var int
	 */
	public $maxResults;

	/**
	 * @var int
	 */
	public $totalCount;

	/**
	 * @var bool
	 */
	public $isNewFormat;

	/**
	 * @var string
	 */
	public $resultsFilePath;

	/**
	 * @var time
	 */
	public $referenceTime;

	private static $map_between_objects = array
	(
		'maxResults' ,
		'resultsFilePath',
		'referenceTime',
		'totalCount',
		'isNewFormat'
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}


	public function toObject($objectToFill = null, $propsToSkip = array())
	{
		if (is_null($objectToFill))
			$objectToFill = new kScheduledTaskJobData();

		/** @var kScheduledTaskJobData $objectToFill */
		$objectToFill = parent::toObject($objectToFill, $propsToSkip);

		return $objectToFill;
	}
} 