<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFlavorAssetListResponse extends KalturaObject
{
	/**
	 * @var KalturaFlavorAssetArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}