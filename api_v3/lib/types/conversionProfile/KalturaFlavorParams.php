<?php
class KalturaFlavorParams extends KalturaObject implements IFilterable 
{
	/**
	 * The id of the Flavor Params
	 * 
	 * @var int
	 * @readonly
	 */
	public $id;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * The name of the Flavor Params
	 * 
	 * @var string 
	 */
	public $name;
	
	/**
	 * The description of the Flavor Params
	 * 
	 * @var string
	 */
	public $description;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 *  
	 * @var int
	 * @readonly
	 */
	public $createdAt;
	
	/**
	 * True if those Flavor Params are part of system defaults
	 * 
	 * @var KalturaNullableBoolean
	 * @readonly
	 * @filter eq
	 */
	public $isSystemDefault;
	
	/**
	 * The Flavor Params tags are used to identify the flavor for different usage (e.g. web, hd, mobile)
	 * 
	 * @var string
	 */
	public $tags;

	/**
	 * The container format of the Flavor Params
	 *  
	 * @var KalturaContainerFormat
	 * @filter eq
	 */
	public $format;
	
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
	 * @var int
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
	
	private static $map_between_objects = array
	(
		"id",
		"partnerId",
		"name",
		"description",
		"createdAt",
		"isSystemDefault" => "isDefault",
		"tags",
		"format",
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
}