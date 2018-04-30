<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFlavorParams extends KalturaAssetParams 
{
	/**
	 * The video codec of the Flavor Params
	 * 
	 * @var KalturaVideoCodec
	 */
	public $videoCodec;
	
	/**
	 * The video bitrate (in KBits) of the Flavor Params
	 * 
	 * @var int
	 */
	public $videoBitrate;
	
	/**
	 * The audio codec of the Flavor Params
	 * 
	 * @var KalturaAudioCodec
	 */
	public $audioCodec;
	
	/**
	 * The audio bitrate (in KBits) of the Flavor Params
	 * 
	 * @var int
	 */
	public $audioBitrate;
	
	/**
	 * The number of audio channels for "downmixing"
	 * 
	 * @var int
	 */
	public $audioChannels;
	
	/**
	 * The audio sample rate of the Flavor Params
	 * 
	 * @var int
	 */
	public $audioSampleRate;
	
	/**
	 * The desired width of the Flavor Params
	 * 
	 * @var int
	 */
	public $width;
	
	/**
	 * The desired height of the Flavor Params
	 * 
	 * @var int
	 */
	public $height;
	
	/**
	 * The frame rate of the Flavor Params
	 * 
	 * @var float
	 */
	public $frameRate;
	
	/**
	 * The gop size of the Flavor Params
	 * 
	 * @var int
	 */
	public $gopSize;
	
	/**
	 * The list of conversion engines (comma separated)
	 * 
	 * @var string
	 */
	public $conversionEngines;
	
	/**
	 * The list of conversion engines extra params (separated with "|")
	 * 
	 * @var string
	 */
	public $conversionEnginesExtraParams;
	
	/**
	 * @var bool
	 */
	public $twoPass;
	
	/**
	 * @var int
	 */
	public $deinterlice;
	
	/**
	 * @var int
	 */
	public $rotate;
	
	/**
	 * @var string
	 */
	public $operators;
	
	/**
	 * @var int
	 */
	public $engineVersion;

	/**
	 * The container format of the Flavor Params
	 *  
	 * @var KalturaContainerFormat
	 * @filter eq
	 */
	public $format;
	
	/**
	 * @var int
	 */
	public $aspectRatioProcessingMode;

	/**
	 * @var int
	 */
	public $forceFrameToMultiplication16;

	/**
	 * @var int
	 */
	public $isGopInSec;

	/**
	 * @var int
	 */
	public $isAvoidVideoShrinkFramesizeToSource;
	
	/**
	 * @var int
	 */
	public $isAvoidVideoShrinkBitrateToSource;
	
	/**
	 * @var int
	 */
	public $isVideoFrameRateForLowBrAppleHls;
	
	/**
	 * @var string;
	 */
	public $multiStream;
	
	/**
	 * @var float
	 *   The pixels in the stored content don't represent visually square pixels, 
	 *   but horizantally stretched pixels. This way the content gets smaller 
	 *   w/out (almost) reducing visual quality.
	 */
	public $anamorphicPixels;
	
	/**
	 * @var int
	 * 	Mezzanine oriented assets does not require alligned and forced 
	 * 	KeyFrames as web/mbr/hls playable assets
	 */
	public $isAvoidForcedKeyFrames;
	
	/**
	 * @var int
	 */
	public $forcedKeyFramesMode;
	
	/**
	 * @var int
	 * 	IMX files (MXF/Mpeg2) have a 32pix black strip at top of the frame.
	 * 	When set - this strip will be removed automatically (just for those files)
	 */
	public $isCropIMX;
	
	/**
	 * @var int
	 */
	public $optimizationPolicy;
	
	/**
	 * @var int
	 * 	Sets max bitrate that is different from the system wide 30fps.
	 *  Required to support mezzanine formats
	 */
	public $maxFrameRate;
	
	/**
	 * @var int
	 */
	public $videoConstantBitrate;

	/**
	 * @var int
	 */
	public $videoBitrateTolerance;

	/**
	 * @var string;
	 */
	public $watermarkData;

	/**
	 * @var string;
	 */
	public $subtitlesData;

	/**
	 * @var int
	 */
	public $isEncrypted;
	
	/**
	 * @var float
	 */
	public $contentAwareness;
	
	/**
	* @var int
	*/
	public $chunkedEncodeMode;

	/**
	 * @var int
	 */
	public $clipOffset;

	/**
	 * @var int
	 */
	public $clipDuration;
	
	private static $map_between_objects = array
	(
		"videoCodec",
		"videoBitrate",
		"audioCodec",
		"audioBitrate",
		"audioChannels",
		"audioSampleRate",
		"width",
		"height",
		"frameRate",
		"gopSize",
		"conversionEngines",
		"conversionEnginesExtraParams",
		"twoPass",
		"deinterlice",
		"rotate",
		"operators",
		"engineVersion",
		"format",
		"aspectRatioProcessingMode",
		"forceFrameToMultiplication16",
		"isGopInSec",
		"isAvoidVideoShrinkFramesizeToSource",
		"isAvoidVideoShrinkBitrateToSource",
		"isVideoFrameRateForLowBrAppleHls",
		"multiStream",
		"anamorphicPixels",
		"isAvoidForcedKeyFrames",
		"forcedKeyFramesMode",
		"isCropIMX",
		"optimizationPolicy",
		"maxFrameRate",
		"videoConstantBitrate",
		"videoBitrateTolerance",
		"watermarkData",
		"subtitlesData",
		"isEncrypted",
		"contentAwareness",
		"chunkedEncodeMode",
		"clipOffset",
		"clipDuration",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($object = null, $skip = array())
	{
		if(is_null($object))
			$object = new flavorParams();
			
		return parent::toObject($object, $skip);
	}
}
