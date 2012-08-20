<?php
/**
 * @package plugins.virusScan
 * @subpackage api.objects
 */
class KalturaVirusScanProfileListResponse extends KalturaObject
{
	/**
	 * @var KalturaVirusScanProfileArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}