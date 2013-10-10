<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaStorageExportJobData extends KalturaStorageJobData
{
    
	/**
	 * @var bool
	 */   	
    public $force;
    
    /**
	 * @var bool
	 */   	
    public $createLink;
	
    
	private static $map_between_objects = array
	(
	    "force",
		"createLink",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kStorageExportJobData();
			
		return parent::toObject($dbData);
	}
	
	/**
	 * @param string $subType
	 * @return int
	 */
	public function toSubType($subType)
	{
		switch ($subType) {
			case KalturaStorageProfileProtocol::FTP:
            case KalturaStorageProfileProtocol::SFTP:
            case KalturaStorageProfileProtocol::SCP:
            case KalturaStorageProfileProtocol::S3:
            case KalturaStorageProfileProtocol::KALTURA_DC:
            case KalturaStorageProfileProtocol::LOCAL:
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
            case StorageProfileProtocol::FTP:
            case StorageProfileProtocol::SFTP:
            case StorageProfileProtocol::SCP:
            case StorageProfileProtocol::S3:
            case StorageProfileProtocol::KALTURA_DC:
          	case StorageProfileProtocol::LOCAL:
                return $subType;                    
            default:
                return kPluginableEnumsManager::coreToApi('StorageProfileProtocol', $subType);
        }
	}
}
