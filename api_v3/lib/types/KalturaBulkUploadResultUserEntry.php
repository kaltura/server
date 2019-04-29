<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBulkUploadResultUserEntry extends KalturaBulkUploadResult
{
	/**
	 * @var int
	 */
	public $userEntryId;

	private static $mapBetweenObjects = array
	(
		"userEntryId",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		return parent::toInsertableObject(new BulkUploadResultUserEntry(), $props_to_skip);
	}
}