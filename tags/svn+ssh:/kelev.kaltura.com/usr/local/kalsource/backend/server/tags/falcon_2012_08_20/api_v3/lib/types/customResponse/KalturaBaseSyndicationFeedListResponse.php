<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBaseSyndicationFeedListResponse extends KalturaObject
{
	/**
	 * @var KalturaBaseSyndicationFeedArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}