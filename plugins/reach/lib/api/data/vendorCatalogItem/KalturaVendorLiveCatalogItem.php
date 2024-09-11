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

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}