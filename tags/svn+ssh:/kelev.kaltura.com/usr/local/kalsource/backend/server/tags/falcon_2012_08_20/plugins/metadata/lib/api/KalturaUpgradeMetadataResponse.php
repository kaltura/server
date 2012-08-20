<?php
/**
 * @package plugins.metadata
 * @subpackage api.objects
 */
class KalturaUpgradeMetadataResponse extends KalturaObject
{
	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;

	/**
	 * @var int
	 * @readonly
	 */
	public $lowerVersionCount;
}