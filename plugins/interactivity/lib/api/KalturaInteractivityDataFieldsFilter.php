<?php

/**
 * @package plugins.interactivity
 * @subpackage api.objects
 */

abstract class KalturaInteractivityDataFieldsFilter extends KalturaObject
{
	/**
	 * A string containing CSV list of fields to include
	 * @var string
	 */
	public $fields;

	protected static $map_between_objects = array
	(
		'fields',
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}