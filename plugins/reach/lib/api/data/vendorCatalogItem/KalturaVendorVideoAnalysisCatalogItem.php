<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorVideoAnalysisCatalogItem extends KalturaVendorCatalogItem
{
	/**
	 * @var KalturaVendorVideoAnalysisType
	 */
	public $videoAnalysisType;

	/**
	 * @var int
	 */
	public $maxVideoDuration;

	private static $map_between_objects = array
	(
		'videoAnalysisType',
		'maxVideoDuration',
	);

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	protected function getServiceFeature(): int
	{
		return VendorServiceFeature::VIDEO_ANALYSIS;
	}

	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill)) {
			$object_to_fill = new VendorVideoAnalysisCatalogItem();
		}

		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill)) {
			$object_to_fill = new VendorVideoAnalysisCatalogItem();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
