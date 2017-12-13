<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorTranslationCatalogItem extends KalturaVendorCaptionsCatalogItem
{
	/**
	 * @var KalturaLanguageArray
	 */
	public $targetLanguages;
	
	private static $map_between_objects = array
	(
		'targetLanguages',
	);
	
	protected function getServiceFeature()
	{
		return KalturaVendorServiceFeature::TRANSLATION;
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
			$object_to_fill = new VendorTranslationCatalogItem();
		
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull(array("targetLanguages"));
		return parent::validateForInsert($propertiesToSkip);
	}
	
	/* (non-PHPdoc)
  	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
  	 */
	public function toObject($sourceObject = null, $propertiesToSkip = array())
	{
		if(is_null($sourceObject))
		{
			$sourceObject = new VendorTranslationCatalogItem();
		}
		
		return parent::toObject($sourceObject, $propertiesToSkip);
	}
}
