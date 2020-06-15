<?php

/**
 * @package plugins.interactivity
 * @subpackage api.objects
 */

class KalturaNodeFieldsFilter extends KalturaInteractivityDataFieldsFilter
{
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new kNodeFieldsFilter();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}