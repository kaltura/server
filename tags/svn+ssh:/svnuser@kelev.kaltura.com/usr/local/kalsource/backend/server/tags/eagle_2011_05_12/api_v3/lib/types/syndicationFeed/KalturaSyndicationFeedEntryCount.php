<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaSyndicationFeedEntryCount extends KalturaObject
{
	/**
	 * the total count of entries that should appear in the feed without flavor filtering
	 * @var int
	 */
	public $totalEntryCount;
	
	/**
	 * count of entries that will appear in the feed (including all relevant filters)
	 * @var int
	 */
	public $actualEntryCount;
	
	/**
	 * count of entries that requires transcoding in order to be included in feed
	 * @var int
	 */
	public $requireTranscodingCount;
}