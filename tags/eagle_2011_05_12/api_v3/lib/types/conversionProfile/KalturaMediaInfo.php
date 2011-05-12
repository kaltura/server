<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaMediaInfo extends KalturaObject implements IFilterable
{
	/**
	 * The id of the media info
	 * 
	 * @var int
	 * @readonly
	 */
	public $id;
	
	/**
	 * The id of the related flavor asset
	 * 
	 * @var string
	 * @filter eq
	 */
	public $flavorAssetId;

	/**
	 * The file size
	 * 
	 * @var int
	 */
	public $fileSize;
	
	/**
	 * The container format
	 * 
	 * @var string
	 */
	public $containerFormat;

	/**
	 * The container id
	 * 
	 * @var string
	 */
	public $containerId;

	/**
	 * The container profile
	 * 
	 * @var string
	 */
	public $containerProfile;
	
	/**
	 * The container duration
	 * 
	 * @var int
	 */
	public $containerDuration;

	/**
	 * The container bit rate
	 * 
	 * @var int
	 */
	public $containerBitRate;

	/**
	 * The video format
	 * 
	 * @var string
	 */
	public $videoFormat;
	
	/**
	 * The video codec id
	 * 
	 * @var string
	 */
	public $videoCodecId;

	/**
	 * The video duration
	 * 
	 * @var int
	 */
	public $videoDuration;
	
	/**
	 * The video bit rate
	 * 
	 * @var int
	 */
	public $videoBitRate;
	
	/**
	 * The video bit rate mode
	 * 
	 * @var KalturaBitRateMode
	 */
	public $videoBitRateMode;

	/**
	 * The video width
	 * 
	 * @var int
	 */
	public $videoWidth;
	
	/**
	 * The video height
	 * 
	 * @var int
	 */
	public $videoHeight;
	
	/**
	 * The video frame rate
	 * 
	 * @var float
	 */
	public $videoFrameRate;
	
	/**
	 * The video display aspect ratio (dar)
	 * 
	 * @var float
	 */
	public $videoDar;
	
	/**
	 * @var int
	 */
	public $videoRotation;
	
	/**
	 * The audio format
	 * 
	 * @var string
	 */
	public $audioFormat;
	
	/**
	 * The audio codec id
	 * 
	 * @var string
	 */
	public $audioCodecId;

	/**
	 * The audio duration
	 * 
	 * @var int
	 */
	public $audioDuration;

	/**
	 * The audio bit rate
	 * 
	 * @var int
	 */
	public $audioBitRate;
	
	/**
	 * The audio bit rate mode
	 * 
	 * @var KalturaBitRateMode
	 */
	public $audioBitRateMode;

	/**
	 * The number of audio channels
	 * 
	 * @var int
	 */
	public $audioChannels;

	/**
	 * The audio sampling rate
	 * 
	 * @var int
	 */
	public $audioSamplingRate;

	/**
	 * The audio resolution
	 * 
	 * @var int
	 */
	public $audioResolution;
	
	/**
	 * The writing library
	 * 
	 * @var string
	 */
	public $writingLib;
	
	/**
	 * The data as returned by the mediainfo command line
	 * 
	 * @var string
	 */
	public $rawData;
	
	/**
	 * @var string
	 */
	public $multiStreamInfo;
	
	/**
	 * @var int
	 */
	public $scanType;
	
	/**
	 * @var string
	 */
	public $multiStream;

	private static $map_between_objects = array
	(
		"id",
		"flavorAssetId",
		"fileSize",
		"containerFormat",
		"containerId",
		"containerProfile",
		"containerDuration",
		"containerBitRate",
		"videoFormat",
		"videoCodecId",
		"videoDuration",
		"videoBitRate",
		"videoBitRateMode",
		"videoWidth",
		"videoHeight",
		"videoFrameRate",
		"videoDar",
		"videoRotation",
		"audioFormat",
		"audioCodecId",
		"audioDuration",
		"audioBitRate",
		"audioBitRateMode",
		"audioChannels",
		"audioSamplingRate",
		"audioResolution",
		"writingLib",
		"rawData",
		"multiStreamInfo",
		"scanType",
		"multiStream",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		return parent::toInsertableObject(new mediaInfo(), $props_to_skip);
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	} 
}