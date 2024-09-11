<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorLiveCatalogItem extends KalturaVendorCaptionsCatalogItem
{
	/**
	 * @var int
	 */
	public $minimalRefundTime;

	/**
	 * @var int
	 */
	public $minimalOrderTime;

	/**
	 * @var int
	 */
	public $durationLimit;


	private static $map_between_objects = array
	(
		'minimalRefundTime',
		'minimalOrderTime',
		'durationLimit',
	);

	protected function getServiceFeature()
	{
		return KalturaVendorServiceFeature::LIVE_CAPTION;
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
			$object_to_fill = new VendorLiveCaptionCatalogItem();
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
			$sourceObject = new VendorLiveCaptionCatalogItem();
		}

		return parent::toObject($sourceObject, $propertiesToSkip);
	}
}