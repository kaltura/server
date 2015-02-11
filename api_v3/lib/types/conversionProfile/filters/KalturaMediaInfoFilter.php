<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaMediaInfoFilter extends KalturaMediaInfoBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new MediaInfoFilter();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaFilter::toObject()
	 */
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		if(!$this->flavorAssetIdEqual)
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, $this->getFormattedPropertyNameWithClassName('flavorAssetIdEqual'));
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
