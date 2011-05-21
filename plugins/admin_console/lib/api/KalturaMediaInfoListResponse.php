<?php
/**
 * @package plugins.adminConsole
 * @subpackage api.objects
 */
class KalturaMediaInfoListResponse extends KalturaObject
{
	/**
	 * @var KalturaMediaInfoArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}