<?php

class KalturaReportResponseOptions extends KalturaObject
{
	/**
	 * @var string
	 */
	public $delimiter;

	/**
	 * @var bool
	 */
	public $skipEmptyDates;

	/**
	 * @var bool
	 */
	public $useFriendlyHeadersNames = false;

	private static $map_between_objects = array
	(
		'delimiter',
		'skipEmptyDates',
		'useFriendlyHeadersNames',
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/* (non-PHPdoc)
 	* @see KalturaObject::toObject()
 	*/
	public function toObject($reportResponseOptions = null, $skip = array())
	{
		if(!$reportResponseOptions)
		{
			$reportResponseOptions = new kReportResponseOptions();
		}

		return parent::toObject($reportResponseOptions, $skip);
	}



}
