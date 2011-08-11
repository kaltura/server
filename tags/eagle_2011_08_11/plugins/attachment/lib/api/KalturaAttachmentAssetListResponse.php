<?php
/**
 * @package plugins.attachment
 * @subpackage api.objects
 */
class KalturaAttachmentAssetListResponse extends KalturaObject
{
	/**
	 * @var KalturaAttachmentAssetArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}