<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorLiveTranslationCatalogItem extends KalturaVendorLiveCatalogItem
{
	/**
	 * @var KalturaCatalogItemLanguage
	 * @filter eq,in
	 */
	public $targetLanguage;

	private static $map_between_objects = array
	(
		'targetLanguage'
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	protected function getServiceFeature()
	{
		return KalturaVendorServiceFeature::LIVE_TRANSLATION;
	}

	protected function validateTargetLanguage(VendorCatalogItem $sourceObject = null)
	{
		if ($this->targetLanguage == KalturaCatalogItemLanguage::AUTO_DETECT)
		{
			throw new KalturaAPIException(KalturaReachErrors::TARGET_LANGUAGE_NOT_SUPPORTED, $this->targetLanguage);
		}
	}

	protected function validate(VendorCatalogItem $sourceObject = null)
	{
		$this->validateTargetLanguage($sourceObject);

		return parent::validate($sourceObject);
	}

	/* (non-PHPdoc)
	* @see KalturaObject::toInsertableObject()
	*/
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
		{
			$object_to_fill = new VendorLiveTranslationCatalogItem();
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
			$sourceObject = new VendorLiveTranslationCatalogItem();
		}

		return parent::toObject($sourceObject, $propertiesToSkip);
	}
}