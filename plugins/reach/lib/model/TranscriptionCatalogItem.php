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
class TranscriptionCatalogItem extends CatalogItem 
{
	const CUSTOM_DATA_SOURCE_LANGUAGES = "source_languages";
	const CUSTOM_DATA_TARGET_LANGUAGES = "target_languages";
	const CUSTOM_DATA_OUTPUT_FORMAT = "target_languages";
	const CUSTOM_DATA_ENABLE_SPEAKER_ID = "enable_speaker_id";
	const CUSTOM_DATA_FIXED_PRICE_ADDONS = "fixed_price_addons";
	
	public function setSourceLanguages($sourceLanguages)
	{
		$this->putInCustomData(self::CUSTOM_DATA_SOURCE_LANGUAGES, $sourceLanguages);
	}
	
	public function setTargetLanguages($targetLanguages)
	{
		$this->putInCustomData(self::CUSTOM_DATA_TARGET_LANGUAGES, $targetLanguages);
	}
	
	public function setOutPutFormat($outPutFormat)
	{
		$this->putInCustomData(self::CUSTOM_DATA_OUTPUT_FORMAT, $outPutFormat);
	}
	
	public function setSpeakerIdEnabled($enableSpeakerId)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ENABLE_SPEAKER_ID, $enableSpeakerId);
	}
	
	public function setFixedPriceAddons($fixedPriceAddons)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIXED_PRICE_ADDONS, $fixedPriceAddons);
	}
	
	public function getSourceLanguages()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_SOURCE_LANGUAGES);
	}
	
	public function getTargetLanguages()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_TARGET_LANGUAGES);
	}
	
	public function getOutPutFormat()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_OUTPUT_FORMAT);
	}
	
	public function getSpeakerIdEnabled()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ENABLE_SPEAKER_ID);
	}
	
	public function getFixedPriceAddons()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIXED_PRICE_ADDONS);
	}

} // VendorCatalogItem
