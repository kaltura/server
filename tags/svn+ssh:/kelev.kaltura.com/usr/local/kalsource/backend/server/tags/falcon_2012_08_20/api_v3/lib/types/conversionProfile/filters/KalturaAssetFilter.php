<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaAssetFilter extends KalturaAssetBaseFilter
{
	protected function validateEntryIdFiltered()
	{
		if(!$this->entryIdEqual && !$this->entryIdIn)
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, $this->getFormattedPropertyNameWithClassName('entryIdEqual') . '/' . $this->getFormattedPropertyNameWithClassName('entryIdIn'));
	}
	
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		$this->validateEntryIdFiltered();
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
