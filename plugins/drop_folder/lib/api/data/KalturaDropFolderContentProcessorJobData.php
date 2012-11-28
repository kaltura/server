<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.objects
 */
class KalturaDropFolderContentProcessorJobData extends KalturaJobData
{
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
	
	
	private static $map_between_objects = array
	(
		"dropFolderFileIds",
		"parsedSlug",
		"contentMatchPolicy",
		"conversionProfileId",
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
}