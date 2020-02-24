<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaGCPStorageProfile extends KalturaStorageProfile
{
	/**
	 * @var KalturaGCPStorageProfileFilesPermissionLevel
	 */
	public $filesPermissionInGCP;

	/**
	 * @var string
	 */
	public $bucketName;

	/**
	 * @var string
	 */
	public $keyFile;

	private static $map_between_objects = array
	(
		'filesPermissionInGCP',
		'bucketName',
		'keyFile',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill)){
			$object_to_fill = new GCPStorageProfile();
		}

		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

}