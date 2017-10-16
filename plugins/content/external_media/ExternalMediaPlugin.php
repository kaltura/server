<?php
/**
 * @package plugins.externalMedia
 */
class ExternalMediaPlugin extends KalturaPlugin implements IKalturaServices, IKalturaObjectLoader, IKalturaEnumerator, IKalturaTypeExtender, IKalturaSearchDataContributor, IKalturaEventConsumers, IKalturaMrssContributor
{
	const PLUGIN_NAME = 'externalMedia';
	const EXTERNAL_MEDIA_CREATED_HANDLER = 'ExternalMediaCreatedHandler';
	const SEARCH_DATA_SUFFIX = 's';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaEventConsumers::getEventConsumers()
	 */
	public static function getEventConsumers()
	{
		return array(
			self::EXTERNAL_MEDIA_CREATED_HANDLER,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaTypeExtender::getExtendedTypes()
	 */
	public static function getExtendedTypes($baseClass, $enumValue)
	{
		if($baseClass == entryPeer::OM_CLASS && $enumValue == entryType::MEDIA_CLIP)
		{
			return array(
				ExternalMediaPlugin::getEntryTypeCoreValue(ExternalMediaEntryType::EXTERNAL_MEDIA),
			);
		}
		
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		$class = self::getObjectClass($baseClass, $enumValue);
		if($class)
			return new $class();
		
		return null;
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'entry' && $enumValue == ExternalMediaPlugin::getEntryTypeCoreValue(ExternalMediaEntryType::EXTERNAL_MEDIA))
		{
			return 'ExternalMediaEntry';
		}
		
		if($baseClass == 'KalturaBaseEntry' && $enumValue == ExternalMediaPlugin::getEntryTypeCoreValue(ExternalMediaEntryType::EXTERNAL_MEDIA))
		{
			return 'KalturaExternalMediaEntry';
		}
		
		return null;
	}

	/**
	 * @return array<string,string> in the form array[serviceName] = serviceClass
	 */
	public static function getServicesMap()
	{
		$map = array(
			'externalMedia' => 'ExternalMediaService',
		);
		return $map;
	}

	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getEntryTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('entryType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
	
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('ExternalMediaEntryType');
	
		if($baseEnumName == 'entryType')
			return array('ExternalMediaEntryType');
			
		return array();
	}

	public static function getExternalSourceSearchData($externalSourceType)
	{
		return self::getPluginName() . $externalSourceType . self::SEARCH_DATA_SUFFIX;
	}

	/* (non-PHPdoc)
	 * @see IKalturaSearchDataContributor::getSearchData()
	 */
	public static function getSearchData(BaseObject $object)
	{
		if($object instanceof ExternalMediaEntry)
		{
			return array('plugins_data' => self::getExternalSourceSearchData($object->getExternalSourceType()));
		}
			
		return null;
	}
	
		/* (non-PHPdoc)
         * @see IKalturaMrssContributor::contribute()
         */
        public function contribute(BaseObject $object, SimpleXMLElement $mrss, kMrssParameters $mrssParams = null)
        {
                if(!($object instanceof entry) || $object->getType() != self::getEntryTypeCoreValue(ExternalMediaEntryType::EXTERNAL_MEDIA))
                        return;

                $externalEntry = $mrss->addChild('externalEntry');
                $externalEntry->addChild('duration', $object->getDuration());
        }

        /* (non-PHPdoc)
         * @see IKalturaMrssContributor::getObjectFeatureType()
         */
        public function getObjectFeatureType()
        {
                return null;
        }

}
