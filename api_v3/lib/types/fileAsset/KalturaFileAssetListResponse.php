<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFileAssetListResponse extends KalturaObject
{
	/**
	 * @var KalturaFileAssetArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}