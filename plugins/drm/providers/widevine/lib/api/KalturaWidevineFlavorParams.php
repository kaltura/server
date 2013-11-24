<?php
/**
 * @package plugins.widevine
 * @subpackage api.objects
 */
class KalturaWidevineFlavorParams extends KalturaFlavorParams 
{
	public function toObject($object = null, $skip = array())
	{
		if(is_null($object))
			$object = new WidevineFlavorParams();
		
		parent::toObject($object, $skip);
		$object->setType(WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR));
		return $object;
	}
}