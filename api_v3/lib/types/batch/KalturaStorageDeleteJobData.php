<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaStorageDeleteJobData extends KalturaStorageJobData
{
	private static $map_between_objects = array
	(
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kStorageDeleteJobData();
			
		return parent::toObject($dbData);
	}
	
	/**
	 * @param string $subType
	 * @return int
	 */
	public function toSubType($subType)
	{
		switch ($subType) {
			case KalturaStorageProfileProtocol::SFTP:
            case KalturaStorageProfileProtocol::FTP:
            case KalturaStorageProfileProtocol::SCP:
            case KalturaStorageProfileProtocol::S3:
            case KalturaStorageProfileProtocol::KALTURA_DC:
                return $subType;	
			default:
				return kPluginableEnumsManager::apiToCore('KalturaStorageProfileProtocol', $subType);
		}
	}
	
	/**
	 * @param int $subType
	 * @return string
	 */
	public function fromSubType($subType)
	{
		switch ($subType) {
            case StorageProfileProtocol::SFTP:
            case StorageProfileProtocol::FTP:
            case StorageProfileProtocol::SCP:
            case StorageProfileProtocol::S3:
            case StorageProfileProtocol::KALTURA_DC:
                return $subType;    
            default:
                return kPluginableEnumsManager::coreToApi('StorageProfileProtocol', $subType);
        }
	}
}

