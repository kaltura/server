<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaAssetFilter extends KalturaAssetBaseFilter
{
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		if(is_null($this->entryIdEqual) && is_null($this->entryIdIn) )
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, $this->getFormattedPropertyNameWithClassName('entryIdEqual') . '/' . $this->getFormattedPropertyNameWithClassName('entryIdIn'));
		
		return $this->toObject($object_to_fill, $props_to_skip);
	}
}
