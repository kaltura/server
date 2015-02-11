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

	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new AssetFilter();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaFilter::toObject()
	 */
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		$this->validateEntryIdFiltered();
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
