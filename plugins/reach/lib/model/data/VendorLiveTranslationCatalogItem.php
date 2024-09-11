<?php
/**
 * @package plugins.reach
 * @subpackage model
 */
class VendorLiveTranslationCatalogItem extends VendorLiveCatalogItem implements IVendorScheduledCatalogItem
{

	const CUSTOM_DATA_TARGET_LANGUAGE = 'target_language';

	public function applyDefaultValues()
	{
		$this->setServiceFeature(VendorServiceFeature::LIVE_TRANSLATION);
	}

	public function setTargetLanguage($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_TARGET_LANGUAGE, $v);
	}

	public function getTargetLanguage()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_TARGET_LANGUAGE, null, true);
	}
}