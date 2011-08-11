<?php
/**
 * @package plugins.metadata
 * @subpackage api.objects
 */
class KalturaMetadataListResponse extends KalturaObject
{
	/**
	 * @var KalturaMetadataArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}