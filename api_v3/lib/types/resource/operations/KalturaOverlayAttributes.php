<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
class KalturaOverlayAttributes extends KalturaObject
{
	/**
	 * Only KalturaEntryResource and KalturaAssetResource are supported
	 * @var KalturaContentResource
	 */
	public $overlayResource;
}
