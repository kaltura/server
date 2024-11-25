<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorSummaryCatalogItem extends KalturaVendorCatalogItem
{
	protected function getServiceFeature(): int
	{
		return VendorServiceFeature::SUMMARY;
	}

	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
		{
			$object_to_fill = new VendorSummaryCatalogItem();
		}

		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
		{
			$object_to_fill = new VendorSummaryCatalogItem();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}