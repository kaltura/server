<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaThumbAssetListResponse extends KalturaObject
{
	/**
	 * @var KalturaThumbAssetArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}