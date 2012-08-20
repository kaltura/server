<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaConversionProfileListResponse extends KalturaObject
{
	/**
	 * @var KalturaConversionProfileArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}