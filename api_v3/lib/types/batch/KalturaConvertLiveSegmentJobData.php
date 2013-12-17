<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaConvertLiveSegmentJobData extends KalturaJobData
{
	/**
	 * Live stream entry id
	 * @var string
	 */
	public $entryId;
	
	/**
	 * Primary or secondary media server
	 * @var KalturaMediaServerIndex
	 */
	public $mediaServerIndex;
	
	/**
	 * The index of the file within the entry
	 * @var int
	 */
	public $fileIndex;
	
	/**
	 * The recorded live media
	 * @var string
	 */
	public $srcFilePath;
	
	/**
	 * The output file
	 * @var string
	 */
	public $destFilePath;
	
	/**
	 * Duration of the live entry including all recorded segments including the current
	 * @var float
	 */
	public $endTime;
	
	private static $map_between_objects = array
	(
		'entryId',
		'mediaServerIndex',
		'fileIndex',
		'srcFilePath',
		'destFilePath',
		'endTime',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kConvertLiveSegmentJobData();
			
		return parent::toObject($dbData, $props_to_skip);
	}
}
