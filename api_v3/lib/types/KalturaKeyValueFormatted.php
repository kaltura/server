<?php

class KalturaKeyValueExtended extends KalturaKeyValue
{
	/**
	 * @var KalturaNullableBoolean
	 */
	public $predefinedFormat;
	
	private static $mapBetweenObjects = array
	(
		"predefinedFormat",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}