<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFeatureStatusListResponse extends KalturaObject
{
	/**
	 * @var KalturaFeatureStatusArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}