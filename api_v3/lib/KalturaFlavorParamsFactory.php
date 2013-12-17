<?php
/**
 * @package api
 * @subpackage objects.factory
 */
class KalturaFlavorParamsFactory
{
	static function getFlavorParamsOutputInstance($type)
	{
		switch ($type) 
		{
			case KalturaAssetType::FLAVOR:
				return new KalturaFlavorParamsOutput();
				
			case KalturaAssetType::THUMBNAIL:
				return new KalturaThumbParamsOutput();
				
			default:
				$obj = KalturaPluginManager::loadObject('KalturaFlavorParamsOutput', $type);
				if($obj)
					return $obj;
					
				return new KalturaFlavorParamsOutput();
		}
	}
	
	static function getFlavorParamsInstance($type)
	{
		switch ($type) 
		{
			case KalturaAssetType::FLAVOR:
				return new KalturaFlavorParams();
				
			case KalturaAssetType::THUMBNAIL:
				return new KalturaThumbParams();
				
			case KalturaAssetType::LIVE:
				return new KalturaLiveParams();
				
			default:
				$obj = KalturaPluginManager::loadObject('KalturaFlavorParams', $type);
				if($obj)
					return $obj;
					
				return new KalturaFlavorParams();
		}
	}
}
