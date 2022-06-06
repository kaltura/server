<?php

class KalturaKeyValueFormatted extends KalturaKeyValue
{
	/**
	 * @var KalturaNullableBoolean
	 */
	public $usePredefinedFormat;
	
	private static $mapBetweenObjects = array
	(
		"usePredefinedFormat",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}