<?php
/**
 * @package plugins.externalMedia
 * @subpackage api.objects
 */
class KalturaExternalMediaEntryListResponse extends KalturaObject
{
	/**
	 * @var KalturaExternalMediaEntryArray
	 * @readonly
	 */
	public $objects;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}