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
	 * @requiresPermission read
	 */
	public $containerId;

	/**
	 * The container profile
	 * 
	 * @var string
	 * @requiresPermission read
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
	 * @requiresPermission read
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
	 * @requiresPermission read
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
	 * @requiresPermission read
	 */
	public $videoDar;
	
	/**
	 * @var int
	 * @requiresPermission read
	 */
	public $videoRotation;
	
	/**
	 * The audio format
	 * 
	 * @var string
	 * @requiresPermission read
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
	 * @requiresPermission read
	 */
	public $audioBitRate;
	
	/**
	 * The audio bit rate mode
	 * 
	 * @var KalturaBitRateMode
	 * @requiresPermission read
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
	 * @requiresPermission read
	 */
	public $audioResolution;
	
	/**
	 * The writing library
	 * 
	 * @var string
	 * @requiresPermission read
	 */
	public $writingLib;
	
	/**
	 * The data as returned by the mediainfo command line
	 * 
	 * @var string
	 * @requiresPermission read
	 */
	public $rawData;
	
	/**
	 * @var string
	 * @requiresPermission read
	 */
	public $multiStreamInfo;
	
	/**
	 * @var int
	 * @requiresPermission read
	 */
	public $scanType;
	
	/**
	 * @var string
	 * @requiresPermission read
	 */
	public $multiStream;

	/**
	 * @var int
	 * @requiresPermission read
	 */
	public $isFastStart;
	
	/**
	 * @var string
	 * @requiresPermission read
	 */
	public $contentStreams;
	
	/**
	 * @var int
	 * @requiresPermission read
	 */
	public $complexityValue;
	
	/**
	 * @var float
	 * @requiresPermission read
	 */
	public $maxGOP;
	
	/**
	 * @var string
	 * @requiresPermission read
	 */
	public $matrixCoefficients;
	
	/**
	 * @var string
	 * @requiresPermission read
	 */
	public $colorTransfer;
	
	/**
	 * @var string
	 * @requiresPermission read
	 */
	public $colorPrimaries;
	
	/**
	 * @var string
	 * @requiresPermission read
	 */
	public $pixelFormat;
	
	/**
	 * @var string
	 * @requiresPermission read
	 */
	public $colorSpace;
	
	/**
	 * @var string
	 * @requiresPermission read
	 */
	public $chromaSubsampling;
	
	/**
	 * @var int
	 * @requiresPermission read
	 */
	public $bitsDepth;
	
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
		"isFastStart",
		"contentStreams",
		"complexityValue",
		"maxGOP",
		"matrixCoefficients",
		"colorTransfer",
		"colorPrimaries",
		"pixelFormat",
		"colorSpace",
		"chromaSubsampling",
		"bitsDepth",
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
