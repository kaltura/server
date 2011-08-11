<?php
/**
 * @package plugins.captionSearch
 * @subpackage api.objects
 */
class KalturaCaptionAssetItemListResponse extends KalturaObject
{
	/**
	 * @var KalturaCaptionAssetItemArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}