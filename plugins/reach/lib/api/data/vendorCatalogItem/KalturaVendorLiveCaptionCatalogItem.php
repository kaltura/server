<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorLiveCaptionCatalogItem extends KalturaVendorLiveCatalogItem
{
	/**
	 * How long before the live stream start should service activate? (in secs)
	 * @var int
	 */
	public $startTimeBuffer;

	/**
	 * How long after the live stream end should service de-activate? (in secs)
	 * @var int
	 */
	public $endTimeBuffer;

	protected function getServiceFeature()
	{
		return KalturaVendorServiceFeature::LIVE_CAPTION;
	}

	private static $map_between_objects = array
	(
		'startTimeBuffer',
		'endTimeBuffer',
	);

	/* (non-PHPdoc)
	 * @see KalturaVendorLiveCaptionCatalogItem::getMapBetweenObjects()
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
