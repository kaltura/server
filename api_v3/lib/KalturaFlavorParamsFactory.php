<?php
class KalturaFlavorParamsFactory
{
	static function getFlavorParamsOutputInstanceByFormat($format)
	{
		switch ($format) 
		{
			case KalturaContainerFormat::_3GP:
			case KalturaContainerFormat::FLV:
			case KalturaContainerFormat::MP4:
			case KalturaContainerFormat::AVI:
			case KalturaContainerFormat::MOV:
			case KalturaContainerFormat::MP3:
			case KalturaContainerFormat::_3GP:
			case KalturaContainerFormat::OGG:
			case KalturaContainerFormat::WMV:
			case KalturaContainerFormat::WMA:
			case KalturaContainerFormat::ISMV:
			case KalturaContainerFormat::MKV:
			case KalturaContainerFormat::WEBM:
				return new KalturaFlavorParamsOutput();
				
			default:
				$obj = KalturaPluginManager::loadObject('KalturaFlavorParamsOutput', $format);
				if($obj)
					return $obj;
					
				return new KalturaFlavorParamsOutput();
		}
	}
	
	static function getFlavorParamsInstanceByFormat($format)
	{
		switch ($format) 
		{
			case KalturaContainerFormat::_3GP:
			case KalturaContainerFormat::FLV:
			case KalturaContainerFormat::MP4:
			case KalturaContainerFormat::AVI:
			case KalturaContainerFormat::MOV:
			case KalturaContainerFormat::MP3:
			case KalturaContainerFormat::_3GP:
			case KalturaContainerFormat::OGG:
			case KalturaContainerFormat::WMV:
			case KalturaContainerFormat::WMA:
			case KalturaContainerFormat::ISMV:
			case KalturaContainerFormat::MKV:
			case KalturaContainerFormat::WEBM:
				return new KalturaFlavorParams();
				
			default:
				$obj = KalturaPluginManager::loadObject('KalturaFlavorParams', $format);
				if($obj)
					return $obj;
					
				return new KalturaFlavorParams();
		}
	}
}
?>