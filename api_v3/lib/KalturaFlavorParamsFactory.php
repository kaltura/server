<?php
class KalturaFlavorParamsFactory
{
	static function getFlavorParamsOutputInstance($type)
	{
		switch ($type) 
		{
			case KalturaAssetType::FLAVOR:
				return new KalturaFlavorParamsOutput();
				
			case KalturaAssetType::THUMBNAIL:
				return new KalturaFlavorParamsOutput();
				
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
				return new KalturaFlavorParams();
				
			default:
				$obj = KalturaPluginManager::loadObject('KalturaFlavorParams', $type);
				if($obj)
					return $obj;
					
				return new KalturaFlavorParams();
		}
	}
}
