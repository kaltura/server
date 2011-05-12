<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBulkUploadListResponse extends KalturaObject
{
	/**
	 * @var KalturaBulkUploads
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}