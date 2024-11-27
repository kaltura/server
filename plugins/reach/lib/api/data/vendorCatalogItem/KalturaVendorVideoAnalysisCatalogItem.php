<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorVideoAnalysisCatalogItem extends KalturaVendorCatalogItem
{
	protected function getServiceFeature(): int
	{
		return VendorServiceFeature::VIDEO_ANALYSIS;
	}

	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill)) {
			$object_to_fill = new VendorVideoAnalysisCatalogItem();
		}

		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill)) {
			$object_to_fill = new VendorVideoAnalysisCatalogItem();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}