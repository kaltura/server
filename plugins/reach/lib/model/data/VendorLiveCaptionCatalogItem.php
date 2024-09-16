<?php
/**
 * @package plugins.reach
 * @subpackage model
 */
class VendorLiveCaptionCatalogItem extends VendorLiveCatalogItem
{
	public function applyDefaultValues()
	{
		$this->setServiceFeature(VendorServiceFeature::LIVE_CAPTION);
	}
}
