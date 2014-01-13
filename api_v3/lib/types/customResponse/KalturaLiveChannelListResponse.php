<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveChannelListResponse extends KalturaObject
{
	/**
	 * @var KalturaLiveChannelArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}