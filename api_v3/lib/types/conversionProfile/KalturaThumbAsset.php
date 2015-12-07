<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaThumbAsset extends KalturaAsset  
{
	/**
	 * The Flavor Params used to create this Flavor Asset
	 * 
	 * @var int
	 * @insertonly
	 * @filter eq,in
	 */
	public $thumbParamsId;
	
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
	 * The status of the asset
	 * 
	 * @var KalturaThumbAssetStatus
	 * @readonly 
	 * @filter eq,in,notin
	 */
	public $status;
	
	private static $map_between_objects = array
	(
		"thumbParamsId" => "flavorParamsId",
		"width",
		"height",
		"status",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new thumbAsset();
		
		if (!is_null($this->thumbParamsId))
		{
			$dbAssetParams = assetParamsPeer::retrieveByPK($this->thumbParamsId);
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
		if($type && $type != KalturaAssetType::THUMBNAIL)
		{
			$pluginObj = KalturaPluginManager::loadObject('KalturaThumbAsset', $type);	
			if($pluginObj)
				return $pluginObj;	
		}
		
		return new KalturaThumbAsset();
	}
}
