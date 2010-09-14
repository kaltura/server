<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPlaylistListResponse extends KalturaObject
{
	/**
	 * @var KalturaPlaylistArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}