<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaControlPanelCommandListResponse extends KalturaObject
{
	/**
	 * @var KalturaControlPanelCommandArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}