<?php
/**
 * @package plugins.adminConsole
 * @subpackage api.objects
 */
class KalturaTrackEntryListResponse extends KalturaObject
{
	/**
	 * @var KalturaTrackEntryArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}