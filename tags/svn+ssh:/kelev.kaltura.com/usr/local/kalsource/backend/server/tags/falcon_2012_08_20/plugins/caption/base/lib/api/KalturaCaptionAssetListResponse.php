<?php
/**
 * @package plugins.caption
 * @subpackage api.objects
 */
class KalturaCaptionAssetListResponse extends KalturaObject
{
	/**
	 * @var KalturaCaptionAssetArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}