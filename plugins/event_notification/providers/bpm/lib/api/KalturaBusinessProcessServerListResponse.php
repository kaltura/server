<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage api.objects
 */
class KalturaBusinessProcessServerListResponse extends KalturaObject
{
	/**
	 * @var KalturaBusinessProcessServerArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}