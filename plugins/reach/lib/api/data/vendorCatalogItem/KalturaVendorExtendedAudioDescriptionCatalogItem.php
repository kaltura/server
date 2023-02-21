<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorExtendedAudioDescriptionCatalogItem extends KalturaVendorCatalogItem
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
	 * @var KalturaVendorCatalogItemOutputFormat
	 * @filter eq,in
	 */
	public $outputFormat;
	
	private static $map_between_objects = array
	(
		'flavorParamsId',
		'clearAudioFlavorParamsId',
		'outputFormat'
	);
	
	protected function getServiceFeature()
	{
		return KalturaVendorServiceFeature::EXTENDED_AUDIO_DESCRIPTION;
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
		{
			$object_to_fill = new VendorExtendedAudioDescriptionCatalogItem();
		}
		
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull(array("sourceLanguage", "flavorParamsId", "clearAudioFlavorParamsId"));
		$this->validateFlavorParamsId($this->flavorParamsId);
		$this->validateFlavorParamsId($this->clearAudioFlavorParamsId);
		
		return parent::validateForInsert($propertiesToSkip);
	}
	
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		/* @var $sourceObject VendorExtendedAudioDescriptionCatalogItem */
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
			$sourceObject = new VendorExtendedAudioDescriptionCatalogItem();
		}
		
		return parent::toObject($sourceObject, $propertiesToSkip);
	}
}
