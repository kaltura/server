<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.objects
 */
class KalturaDropFolderFileListResponse extends KalturaObject
{
	/**
	 * @var KalturaDropFolderFileArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}