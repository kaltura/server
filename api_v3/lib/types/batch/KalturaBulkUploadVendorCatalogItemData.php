<?php
/**
 * This class represents object-specific data passed to the
 * bulk upload job.
 * @package api
 * @subpackage objects
 *
 */
class KalturaBulkUploadVendorCatalogItemData extends KalturaBulkUploadObjectData
{
	private static $map_between_objects = array
	(

	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new kBulkUploadObjectData();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}

}