<?php
/**
 * @package plugins.reach
 * @subpackage model
 */
class VendorLiveTranslationCatalogItem extends VendorLiveCatalogItem
{
	public function applyDefaultValues()
	{
		$this->setServiceFeature(VendorServiceFeature::LIVE_TRANSLATION);
	}
}