<?php
class KalturaSupportedSubTypes extends KalturaObject 
{
	/**
	 * list of comma separated supported sub types
	 * 
	 * @var string
	 */
	public $supportedSubTypes;

	private static $mapBetweenObjects = array
	(
		"supportedSubTypes",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}