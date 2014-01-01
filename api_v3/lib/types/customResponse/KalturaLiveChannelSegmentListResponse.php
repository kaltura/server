<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveChannelSegmentListResponse extends KalturaObject
{
	/**
	 * @var KalturaLiveChannelSegmentArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}