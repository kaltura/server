<?php
/**
 * @package api
 * @subpackage objects.factory
 */
class KalturaAssetParamsFactory
{
	static function getAssetParamsOutputInstance($type)
	{
		switch ($type) 
		{
			case KalturaAssetType::FLAVOR:
				return new KalturaFlavorParamsOutput();
				
			case KalturaAssetType::THUMBNAIL:
				return new KalturaThumbParamsOutput();
				
			default:
				$obj = KalturaPluginManager::loadObject('KalturaAssetParamsOutput', $type);
				if($obj)
					return $obj;
					
				return new KalturaFlavorParamsOutput();
		}
	}
	
	static function getAssetParamsInstance($type)
	{
		switch ($type) 
		{
			case KalturaAssetType::FLAVOR:
				return new KalturaFlavorParams();
				
			case KalturaAssetType::THUMBNAIL:
				return new KalturaThumbParams();
				
			default:
				$obj = KalturaPluginManager::loadObject('KalturaAssetParams', $type);
				if($obj)
					return $obj;
					
				return new KalturaFlavorParams();
		}
	}
}
