<?php
class KalturaFlavorAsset extends KalturaAsset 
{
	/**
	 * The Flavor Params used to create this Flavor Asset
	 * 
	 * @var int
	 * @readonly
	 */
	public $flavorParamsId;
	
	/**
	 * The width of the Flavor Asset 
	 * 
	 * @var int
	 * @readonly
	 */
	public $width;
	
	/**
	 * The height of the Flavor Asset
	 * 
	 * @var int
	 * @readonly
	 */
	public $height;
	
	/**
	 * The overall bitrate (in KBits) of the Flavor Asset 
	 * 
	 * @var int
	 * @readonly
	 */
	public $bitrate;
	
	/**
	 * The frame rate (in FPS) of the Flavor Asset
	 * 
	 * @var int
	 * @readonly
	 */
	public $frameRate;
	
	/**
	 * True if this Flavor Asset is the original source
	 * 
	 * @var bool
	 */
	public $isOriginal;
	
	/**
	 * True if this Flavor Asset is playable in KDP
	 * 
	 * @var bool
	 */
	public $isWeb;
	
	/**
	 * The container format
	 * 
	 * @var string
	 */
	public $containerFormat;
	
	/**
	 * The video codec
	 * 
	 * @var string
	 */
	public $videoCodecId;
	
	
	private static $map_between_objects = array
	(
		"width",
		"height",
		"bitrate",
		"size",
		"isOriginal",
		"isWeb",
		"containerFormat",
	);
	
}