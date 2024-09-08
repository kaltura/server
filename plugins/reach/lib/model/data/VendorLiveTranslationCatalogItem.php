<?php
/**
 * @package plugins.reach
 * @subpackage model
 */
class VendorLiveTranslationCatalogItem extends VendorLiveCaptionCatalogItem
{
	public function applyDefaultValues()
	{
		$this->setServiceFeature(VendorServiceFeature::LIVE_TRANSLATION);
	}
}