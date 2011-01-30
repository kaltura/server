<?php
/**
 * @package plugins.shortLink
 * @subpackage api.objects
 */
class KalturaShortLinkListResponse extends KalturaObject
{
	/**
	 * @var KalturaShortLinkArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}