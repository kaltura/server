<?php
/**
 * @package plugins.drm
 * @subpackage api.objects
 */
class KalturaDrmProfileListResponse extends KalturaObject
{
	/**
	 * @var KalturaDrmProfileArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}