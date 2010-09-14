<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveStreamListResponse extends KalturaObject
{
	/**
	 * @var KalturaLiveStreamEntryArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}