<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaSearchResultResponse extends KalturaObject
{
	/**
	 * @var KalturaSearchResultArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var bool
	 * @readonly
	 */
	public $needMediaInfo;
}