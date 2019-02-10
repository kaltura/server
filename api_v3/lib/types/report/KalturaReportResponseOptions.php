<?php

class KalturaReportResponseOptions extends KalturaObject
{
	/**
	 * @var string
	 */
	public $delimiter;

	public function __construct()
	{
		$this->delimiter = ',';
	}

	private static $map_between_objects = array
	(
		'delimiter',
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
			$reportResponseOptions = new reportResponseOptions();

		return parent::toObject($reportResponseOptions, $skip);
	}



}