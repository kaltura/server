<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.objects
 */
class KalturaDropFolderContentProcessorJobData extends KalturaJobData
{
	
	/**
	 * @var int
	 */
	public $dropFolderId;
	
	/**
	 * @var string
	 */
	public $dropFolderFileIds;
	
	/**
	 * @var string
	 */
	public $parsedSlug;
	
	/**
	 * @var KalturaDropFolderContentFileHandlerMatchPolicy
	 */
	public $contentMatchPolicy;
	
	/**
	 * @var int
	 */
	public $conversionProfileId;
	
	/**
	 * @var string
	 */
	public $parsedUserId;
	
	private static $map_between_objects = array
	(
		"dropFolderId",
		"dropFolderFileIds",
		"parsedSlug",
		"contentMatchPolicy",
		"conversionProfileId",
		"parsedUserId",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kDropFolderContentProcessorJobData();
		
		return parent::toObject($dbData, $props_to_skip);
	}

	
	/**
	 * @param string $subType
	 * @return int
	 */
	public function toSubType($subType)
	{
		switch ($subType) {
			case KalturaDropFolderType::FTP:
            case KalturaDropFolderType::SFTP:
            case KalturaDropFolderType::SCP:
            case KalturaDropFolderType::S3:
                return $subType;                  	
			default:
				return kPluginableEnumsManager::apiToCore('KalturaDropFolderType', $subType);
		}
	}
	
	/**
	 * @param int $subType
	 * @return string
	 */
	public function fromSubType($subType)
	{
		switch ($subType) {
            case DropFolderType::FTP:
            case DropFolderType::SFTP:
            case DropFolderType::SCP:
            case DropFolderType::S3:
                return $subType;                    
            default:
                return kPluginableEnumsManager::coreToApi('DropFolderType', $subType);
        }
	}
}