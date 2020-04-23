<?php
/**
 * Subclass for representing a row from the 'bulk_upload_result' table.
 *
 *
 *
 * @package Core
 * @subpackage model
 */
class BulkUploadResultVendorCatalogItem extends BulkUploadResult
{
	const VENDOR_CATALOG_ITEM_ID = 'vendorCatalogItemId';
	const VENDOR_PARTNER_ID = 'vendorPartnerId';
	const NAME = 'name';
	const SYSTEM_NAME = 'systemName';
	const SERVICE_FEATURE = 'serviceFeature';
	const SERVICE_TYPE = 'serviceType';
	const TURN_AROUND_TIME = 'turnAroundTime';
	const SOURCE_LANGUAGE = 'sourceLanguage';
	const TARGET_LANGUAGE = 'targetLanguage';
	const OUTPUT_FORMAT = 'outputFormat';
	const ENABLE_SPEAKER_ID = 'enableSpeakerId';
	const FIXED_PRICE_ADDONS = 'fixedPriceAddons';
	const PRICING = 'pricing';
	const FLAVOR_PARAMS_ID = 'flavorParamsId';
	const CLEAR_AUDIO_FLAVOR_PARAMS_ID = 'clearAudioFlavorParamsId';

	/* (non-PHPdoc)
	 * @see BulkUploadResult::handleRelatedObjects()
	 */
	public function handleRelatedObjects()
	{
		$vendorCatalogItem = $this->getObject();
		if ($vendorCatalogItem)
		{
			$vendorCatalogItem->setBulkUploadId($this->getBulkUploadJobId());
			$vendorCatalogItem->save();
		}
	}

	/* (non-PHPdoc)
	 * @see BulkUploadResult::getObject()
	 */
	public function getObject()
	{
		return VendorCatalogItemPeer::retrieveByPKNoFilter($this->getObjectId());
	}

	public function getVendorCatalogItemId() {return $this->getFromCustomData(self::VENDOR_CATALOG_ITEM_ID);}
	public function setVendorCatalogItemId($v) {$this->putInCustomData(self::VENDOR_CATALOG_ITEM_ID, $v);}

	public function getVendorPartnerId() {return $this->getFromCustomData(self::VENDOR_PARTNER_ID);}
	public function setVendorPartnerId($v) {$this->putInCustomData(self::VENDOR_PARTNER_ID, $v);}

	public function getName() {return $this->getFromCustomData(self::NAME);}
	public function setName($v) {$this->putInCustomData(self::NAME, $v);}

	public function getSystemName() {return $this->getFromCustomData(self::SYSTEM_NAME);}
	public function setSystemName($v) {$this->putInCustomData(self::SYSTEM_NAME, $v);}

	public function getServiceFeature() {return $this->getFromCustomData(self::SERVICE_FEATURE);}
	public function setServiceFeature($v) {$this->putInCustomData(self::SERVICE_FEATURE, $v);}

	public function getServiceType() {return $this->getFromCustomData(self::SERVICE_TYPE);}
	public function setServiceType($v) {$this->putInCustomData(self::SERVICE_TYPE, $v);}

	public function getTurnAroundTime() {return $this->getFromCustomData(self::TURN_AROUND_TIME);}
	public function setTurnAroundTime($v) {$this->putInCustomData(self::TURN_AROUND_TIME, $v);}

	public function getSourceLanguage() {return $this->getFromCustomData(self::SOURCE_LANGUAGE);}
	public function setSourceLanguage($v) {$this->putInCustomData(self::SOURCE_LANGUAGE, $v);}

	public function getTargetLanguage() {return $this->getFromCustomData(self::TARGET_LANGUAGE);}
	public function setTargetLanguage($v) {$this->putInCustomData(self::TARGET_LANGUAGE, $v);}

	public function getOutputFormat() {return $this->getFromCustomData(self::OUTPUT_FORMAT);}
	public function setOutputFormat($v) {$this->putInCustomData(self::OUTPUT_FORMAT, $v);}

	public function getEnableSpeakerId() {return $this->getFromCustomData(self::ENABLE_SPEAKER_ID);}
	public function setEnableSpeakerId($v) {$this->putInCustomData(self::ENABLE_SPEAKER_ID, $v);}

	public function getFixedPriceAddons() {return $this->getFromCustomData(self::FIXED_PRICE_ADDONS);}
	public function setFixedPriceAddons($v) {$this->putInCustomData(self::FIXED_PRICE_ADDONS, $v);}

	public function getPricing() {return $this->getFromCustomData(self::PRICING);}
	public function setPricing($v) {$this->putInCustomData(self::PRICING, $v);}

	public function getFlavorParamsId() {return $this->getFromCustomData(self::FLAVOR_PARAMS_ID);}
	public function setFlavorParamsId($v) {$this->putInCustomData(self::FLAVOR_PARAMS_ID, $v);}

	public function getClearAudioFlavorParamsId() {return $this->getFromCustomData(self::CLEAR_AUDIO_FLAVOR_PARAMS_ID);}
	public function setClearAudioFlavorParamsId($v) {$this->putInCustomData(self::CLEAR_AUDIO_FLAVOR_PARAMS_ID, $v);}

}