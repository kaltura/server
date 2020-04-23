<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBulkUploadResultVendorCatalogItem extends KalturaBulkUploadResult
{
	/**
	 * @var int
	 */
	public $vendorCatalogItemId;

	/**
	 * @var int
	 */
	public $vendorPartnerId;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $systemName;

	/**
	 * @var KalturaVendorServiceFeature
	 */
	public $serviceFeature;

	/**
	 * @var KalturaVendorServiceType
	 */
	public $serviceType;

	/**
	 * @var KalturaVendorServiceTurnAroundTime
	 */
	public $turnAroundTime;

	/**
	 * @var KalturaCatalogItemLanguage
	 */
	public $sourceLanguage;

	/**
	 * @var KalturaCatalogItemLanguage
	 */
	public $targetLanguage;

	/**
	 * @var KalturaVendorCatalogItemOutputFormat
	 */
	public $outputFormat;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableSpeakerId;

	/**
	 * @var int
	 */
	public $fixedPriceAddons;

	/**
	 * @var KalturaVendorCatalogItemPricing
	 */
	public $pricing;

	/**
	 * @var int
	 */
	public $flavorParamsId;

	/**
	 * @var int
	 */
	public $clearAudioFlavorParamsId;

	private static $mapBetweenObjects = array  //todo maybe needed to convert
	(
		'vendorCatalogItemId',
		'vendorPartnerId',
		'name',
		'systemName',
		'serviceFeature',
		'serviceType',
		'turnAroundTime',
		'sourceLanguage',
		'targetLanguage',
		'outputFormat',
		'enableSpeakerId',
		'fixedPriceAddons',
		'pricing',
		'flavorParamsId',
		'clearAudioFlavorParamsId'
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		return parent::toInsertableObject(new BulkUploadResultVendorCatalogItem(), $props_to_skip);
	}

	/* (non-PHPdoc)
     * @see KalturaObject::toObject()
     */
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if ($this->vendorCatalogItemId)
		{
			$this->objectId = $this->vendorCatalogItemId;
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}

}