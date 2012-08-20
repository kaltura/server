<?php
/**
 * @package plugins.annotation
 * @subpackage api.objects
 */
class KalturaAnnotationListResponse extends KalturaObject
{
	/**
	 * @var KalturaAnnotationArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}