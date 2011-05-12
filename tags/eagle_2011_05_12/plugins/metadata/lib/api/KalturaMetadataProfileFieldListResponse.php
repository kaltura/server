<?php
/**
 * @package plugins.metadata
 * @subpackage api.objects
 */
class KalturaMetadataProfileFieldListResponse extends KalturaObject
{
	/**
	 * @var KalturaMetadataProfileFieldArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}