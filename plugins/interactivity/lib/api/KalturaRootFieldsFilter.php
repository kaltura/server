<?php

/**
 * @package plugins.interactivity
 * @subpackage api.objects
 */

class KalturaRootFieldsFilter extends KalturaInteractivityDataFieldsFilter
{
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new kRootFieldsFilter();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}