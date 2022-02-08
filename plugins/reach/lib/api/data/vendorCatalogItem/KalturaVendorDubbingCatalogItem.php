<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorDubbingCatalogItem extends KalturaVendorCatalogItem
{
	
	/**
	 * @var int
	 */
	public $flavorParamsId;
	
	/**
	 * @var int
	 */
	public $clearAudioFlavorParamsId;
	
	/**
	 * @var KalturaCatalogItemLanguage
	 * @filter eq,in
	 */
	public $targetLanguage;
	
	private static $map_between_objects = array
	(
		'flavorParamsId',
		'clearAudioFlavorParamsId',
		'targetLanguage',
	);
	
	protected function getServiceFeature()
	{
		return KalturaVendorServiceFeature::DUBBING;
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
	 * */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
		{
			$object_to_fill = new VendorDubbingCatalogItem();
		}
		
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull(array("sourceLanguage", "targetLanguage", "flavorParamsId", "clearAudioFlavorParamsId"));
		$this->validateFlavorParamsId($this->flavorParamsId);
		$this->validateFlavorParamsId($this->clearAudioFlavorParamsId);
		
		return parent::validateForInsert($propertiesToSkip);
	}
	
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		/* @var $sourceObject VendorDubbingCatalogItem */
		if(isset($this->flavorParamsId) && $this->flavorParamsId != $sourceObject->getFlavorParamsId())
		{
			$this->validateFlavorParamsId($this->flavorParamsId);
		}
		
		if(isset($this->clearAudioFlavorParamsId) && $this->clearAudioFlavorParamsId != $sourceObject->getClearAudioFlavorParamsId())
		{
			$this->validateFlavorParamsId($this->clearAudioFlavorParamsId);
		}
		
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	protected function validateFlavorParamsId($id)
	{
		$flavorParams = assetParamsPeer::retrieveByPK($id);
		if (!$flavorParams)
		{
			throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND, $id);
		}
	}
	
	/* (non-PHPdoc)
 	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
 	 */
	public function toObject($sourceObject = null, $propertiesToSkip = array())
	{
		if(is_null($sourceObject))
		{
			$sourceObject = new VendorDubbingCatalogItem();
		}
		
		return parent::toObject($sourceObject, $propertiesToSkip);
	}
}