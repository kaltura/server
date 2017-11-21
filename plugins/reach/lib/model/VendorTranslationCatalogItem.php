<?php


/**
 * Skeleton subclass for representing a row from the 'vendor_catalog_item' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.reach
 * @subpackage model
 */
class VendorTranslationCatalogItem extends VendorCaptionsCatalogItem 
{
	public function applyDefaultValues()
	{
		$this->setServiceFeature(VendorServiceFeature::TRANSLATION);
	}
	
	const CUSTOM_DATA_OUTPUT_FORMAT = "target_languages";
	
	public function setTargetLanguages($targetLanguages)
	{
		$this->putInCustomData(self::CUSTOM_DATA_TARGET_LANGUAGES, $targetLanguages);
	}
	
	public function getTargetLanguages()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_TARGET_LANGUAGES);
	}

} // VendorTranslationCatalogItem
