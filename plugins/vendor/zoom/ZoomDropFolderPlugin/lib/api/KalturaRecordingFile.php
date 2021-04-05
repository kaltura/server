<?php
/**
 * @package plugins.ZoomDropFolder
 * @subpackage api.objects
 */
class KalturaRecordingFile extends KalturaObject
{
	/**
	 * @var string
	 */
	public $id;

	/**
	 * @var string
	 */
	public $recordingStart;

	/**
	 * @var KalturaRecordingFileType
	 */
	public $fileType;

	/**
	 * @var string
	 */
	public $downloadUrl;
	
	/**
	 * @var string
	 */
	public $fileExtension;
	
	/**
	 * @var string
	 */
	public $downloadToken;
	

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array(
		'id',
		'recordingStart',
		'fileType',
		'downloadUrl',
		'fileExtension',
		'downloadToken'
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (!$dbObject)
			$dbObject = new kRecordingFile();

		return parent::toObject($dbObject, $skip);
	}
}
