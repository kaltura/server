<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUploadTokenListResponse extends KalturaObject
{
	/**
	 * @var KalturaUploadTokenArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}