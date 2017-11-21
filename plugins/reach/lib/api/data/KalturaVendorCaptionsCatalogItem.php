<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorCaptionsCatalogItem extends KalturaVendorCatalogItem
{
	/**
	 * @var KalturaLanguageArray
	 */
	public $sourceLanguages;
	
	/**
	 * @var KalturaVendorCatalogItemOutputFormat
	 */
	public $outPutFormat;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableSpeakerId;
	
	/**
	 * @var int
	 */
	public $fixedPriceAddons;
	
	
	private static $map_between_objects = array
	(
		'sourceLanguages',
		'outPutFormat',
		'enableSpeakerId',
		'fixedPriceAddons'
	);
	
	protected function getServiceFeature()
	{
		return KalturaVendorServiceFeature::CAPTIONS;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
 * @see KalturaObject::toInsertableObject()
 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
			$object_to_fill = new VendorCaptionsCatalogItem();
		
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull(array("sourceLanguages", "outPutFormat"));
		return parent::validateForInsert($propertiesToSkip);
	}
}
