<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaRemotePathListResponse extends KalturaObject
{
	/**
	 * @var KalturaRemotePathArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}