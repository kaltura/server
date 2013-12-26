<?php
/**
 * @package plugins.drm
 * @subpackage api.objects
 */
class KalturaDrmPolicyListResponse extends KalturaObject
{
	/**
	 * @var KalturaDrmPolicyArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}