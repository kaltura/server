<?php
/**
 * @package plugins.document
 * @subpackage api.objects
 */
class KalturaDocumentListResponse extends KalturaObject
{
	/**
	 * @var KalturaDocumentEntryArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}