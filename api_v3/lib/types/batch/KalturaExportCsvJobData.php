<?php
/**
 * @package api
 * @subpackage objects
 */

class KalturaExportCsvJobData extends KalturaJobData
{
	/**
	 * The users name
	 * @var string
	 */
	public $userName;
	
	/**
	 * The users email
	 * @var string
	 */
	public $userMail;
	
	/**
	 * The file location
	 * @var string
	 */
	public $outputPath;

	/**
	 * @var string
	 */
	public $sharedOutputPath;
	
	
	private static $map_between_objects = array
	(
		'userMail',
		'userName',
		'outputPath',
		"sharedOutputPath",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
		{
			throw new KalturaAPIException(KalturaErrors::OBJECT_TYPE_ABSTRACT, "KalturaExportCsvJobData");
		}
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
	
	/**
	 * @param string $subType is the bulk upload sub type
	 * @return int
	 */
	public function toSubType($subType)
	{
		if(is_null($subType))
			return null;
		
		return kPluginableEnumsManager::apiToCore('ExportObjectType', $subType);
	}
	
	/**
	 * @param int $subType
	 * @return string
	 */
	public function fromSubType($subType)
	{
		if(is_null($subType))
			return null;
		
		return kPluginableEnumsManager::coreToApi('ExportObjectType', $subType);
	}
	
}
