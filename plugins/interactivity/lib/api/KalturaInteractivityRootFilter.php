<?php

/**
 * @package plugins.interactivity
 * @subpackage api.objects
 */

class KalturaInteractivityRootFilter extends KalturaInteractivityDataFieldsFilter
{
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new kInteractivityRootFilter();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}