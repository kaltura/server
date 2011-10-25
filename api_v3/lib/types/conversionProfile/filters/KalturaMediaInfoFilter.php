<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaMediaInfoFilter extends KalturaMediaInfoBaseFilter
{
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		if(is_null($this->flavorAssetIdEqual))
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, $this->getFormattedPropertyNameWithClassName('flavorAssetIdEqual'));
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
