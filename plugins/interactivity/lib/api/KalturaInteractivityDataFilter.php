<?php

/**
 * @package plugins.interactivity
 * @subpackage api.objects
 */

class KalturaInteractivityDataFilter extends KalturaObject
{
	/**
	 * @var KalturaInteractivityRootFilter
	 */
	public $rootFilter;

	/**
	 * @var KalturaInteractivityNodeFilter
	 */
	public $nodeFilter;

	/**
	 * @var KalturaInteractivityInteractionFilter
	 */
	public $interactionFilter;

	protected static $map_between_objects = array
	(
		'rootFilter',
		'nodeFilter',
		'interactionFilter',
	);

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new kInteractivityDataFilter();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}