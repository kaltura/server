<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchCaptionItem extends KalturaESearchItem {
	
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new ESearchCaptionItem();
		return parent::toObject($object_to_fill, $props_to_skip);
	}

}
