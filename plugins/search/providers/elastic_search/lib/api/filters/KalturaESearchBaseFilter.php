<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.filters
 */
abstract class KalturaESearchBaseFilter extends KalturaObject
{
	private static $mapBetweenObjects = array();

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

}
