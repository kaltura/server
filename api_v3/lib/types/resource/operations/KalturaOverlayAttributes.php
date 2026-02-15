<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaOverlayAttributes extends KalturaMediaCompositionAttributes
{
	/**
	 * Only KalturaEntryResource and KalturaAssetResource are supported
	 * @var KalturaContentResource
	 */
	public $resource;

	/**
	 * Only KalturaReplaceBackgroundAttributes is supported
	 * @var KalturaMediaCompositionAttributesArray
	 */
	public $resourceMediaCompositionAttributesArray;
}
