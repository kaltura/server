<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaTranscriptionCatalogItem extends KalturaCatalogItem
{
	/**
	 * @var KalturaLanguageArray
	 */
	public $sourceLanguages;
	
	/**
	 * @var KalturaLanguageArray
	 */
	public $targetLanguages;
	
	/**
	 * @var KalturaCatalogItemOutputFormat
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
		'targetLanguages',
		'outPutFormat',
		'enableSpeakerId',
		'fixedPriceAddons'
	);
	
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
			$object_to_fill = new TranscriptionCatalogItem();
		
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull(array("sourceLanguages", "targetLanguages", "outPutFormat"));
		return parent::validateForInsert($propertiesToSkip);
	}
}
