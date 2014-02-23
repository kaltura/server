<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFlavorAsset extends KalturaAsset 
{
	/**
	 * The Flavor Params used to create this Flavor Asset
	 * 
	 * @var int
	 * @insertonly
	 * @filter eq,in
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
	 * @var float
	 * @readonly
	 */
	public $frameRate;
	
	/**
	 * True if this Flavor Asset is the original source
	 * 
	 * @var bool
	 * @readonly
	 */
	public $isOriginal;
	
	/**
	 * True if this Flavor Asset is playable in KDP
	 * 
	 * @var bool
	 * @readonly
	 */
	public $isWeb;
	
	/**
	 * The container format
	 * 
	 * @var string
	 * @readonly
	 */
	public $containerFormat;
	
	/**
	 * The video codec
	 * 
	 * @var string
	 * @readonly
	 */
	public $videoCodecId;
	
	/**
	 * The status of the Flavor Asset
	 * 
	 * @var KalturaFlavorAssetStatus
	 * @readonly 
	 * @filter eq,in,notin
	 */
	public $status;
	
	
	private static $map_between_objects = array
	(
		"flavorParamsId",
		"width",
		"height",
		"bitrate",
		"frameRate",
		"isOriginal",
		"isWeb",
		"containerFormat",
		"videoCodecId",
		"status",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if (!is_null($this->flavorParamsId))
		{
			$dbAssetParams = assetParamsPeer::retrieveByPK($this->flavorParamsId);
			if ($dbAssetParams)
			{
				$object_to_fill->setFromAssetParams($dbAssetParams);
			}
		}
		
		return parent::toInsertableObject ($object_to_fill, $props_to_skip);
	}
	
	/**
	 * @param int $type
	 * @return KalturaFlavorAsset
	 */
	static function getInstanceByType ($type = null)
	{
		if(!$type || $type == KalturaAssetType::FLAVOR)
		{
			$obj = new KalturaFlavorAsset();
		}
		elseif($type == KalturaAssetType::LIVE)
		{
			$obj = new KalturaLiveAsset();
		}
		else 
		{
			$obj = KalturaPluginManager::loadObject('KalturaFlavorAsset', $type);	
			if(!$obj)
				$obj = new KalturaFlavorAsset();	
		}
		return $obj;
	}
}
