<?php
/**
 * Enable custom metadata on code cue point objects
 * @package plugins.codeCuePoint
 */
class CodeCuePointMetadataPlugin extends KalturaPlugin implements IKalturaPending, IKalturaEnumerator, IKalturaCuePointXmlParser
{
	const PLUGIN_NAME = 'codeCuePointMetadata';
	const METADATA_PLUGIN_NAME = 'metadata';
	const METADATA_PLUGIN_VERSION_MAJOR = 2;
	const METADATA_PLUGIN_VERSION_MINOR = 0;
	const METADATA_PLUGIN_VERSION_BUILD = 0;
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn()
	{
		$metadataVersion = new KalturaVersion(
			self::METADATA_PLUGIN_VERSION_MAJOR,
			self::METADATA_PLUGIN_VERSION_MINOR,
			self::METADATA_PLUGIN_VERSION_BUILD);
			
		$metadataDependency = new KalturaDependency(self::METADATA_PLUGIN_NAME, $metadataVersion);
		$codeCuePointDependency = new KalturaDependency(CodeCuePointPlugin::getPluginName());
		$codeCuePointMetadataDependency = new KalturaDependency(CuePointMetadataPlugin::getPluginName());
		
		return array($metadataDependency, $codeCuePointDependency, $codeCuePointMetadataDependency);
	}

	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('CodeCuePointMetadataObjectType');
	
		if($baseEnumName == 'MetadataObjectType')
			return array('CodeCuePointMetadataObjectType');
			
		return array();
	}
	
	public static function getMetadataObjectTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('MetadataObjectType', $value);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaCuePointXmlParser::getApiValue()
	 */
	public static function parseXml(SimpleXMLElement $scene, $partnerId, CuePoint $cuePoint = null)
	{
		if(is_null($cuePoint) || $scene->getName() != 'scene-code-cue-point' || !($cuePoint instanceof CodeCuePoint))
			return $cuePoint;
			
		$objectType = self::getMetadataObjectTypeCoreValue(CodeCuePointMetadataObjectType::CODE_CUE_POINT);
		return CuePointMetadataPlugin::parseXml($objectType, $scene, $partnerId, $cuePoint);
	}
}
