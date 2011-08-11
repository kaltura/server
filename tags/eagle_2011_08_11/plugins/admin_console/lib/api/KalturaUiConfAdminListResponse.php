<?php
/**
 * @package plugins.adminConsole
 * @subpackage api.objects
 */
class KalturaUiConfAdminListResponse extends KalturaObject
{
	/**
	 * @var KalturaUiConfAdminArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}