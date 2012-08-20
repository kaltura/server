<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaModerationFlagListResponse extends KalturaObject
{
	/**
	 * @var KalturaModerationFlagArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}