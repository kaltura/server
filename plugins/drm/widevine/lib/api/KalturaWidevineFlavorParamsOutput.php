<?php
/**
 * @package plugins.widevine
 * @subpackage api.objects
 */
class KalturaWidevineFlavorParamsOutput extends KalturaFlavorParamsOutput 
{
	public function toObject($object = null, $skip = array())
	{
		if(is_null($object))
			$object = new WidevineFlavorParamsOutput();
		
		parent::toObject($object, $skip);
		$object->setType(WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR));
		return $object;
	}
}