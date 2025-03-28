<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorLiveCaptionCatalogItem extends KalturaVendorLiveCatalogItem
{
	protected function getServiceFeature()
	{
		return KalturaVendorServiceFeature::LIVE_CAPTION;
	}

	/* (non-PHPdoc)
 * @see KalturaObject::toInsertableObject()
 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
		{
			$object_to_fill = new VendorLiveCaptionCatalogItem();
		}

		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	/* (non-PHPdoc)
 	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
 	 */
	public function toObject($sourceObject = null, $propertiesToSkip = array())
	{
		if(is_null($sourceObject))
		{
			$sourceObject = new VendorLiveCaptionCatalogItem();
		}

		return parent::toObject($sourceObject, $propertiesToSkip);
	}
}