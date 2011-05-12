<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBatchJobListResponse extends KalturaObject
{
	/**
	 * @var KalturaBatchJobArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}