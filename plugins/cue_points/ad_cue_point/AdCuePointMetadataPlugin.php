<?php
/**
 * Enable custom metadata on ad cue point objects
 * @package plugins.adCuePoint
 */
class AdCuePointMetadataPlugin extends KalturaPlugin implements IKalturaPending, IKalturaSchemaContributor, IKalturaEnumerator
{
	const PLUGIN_NAME = 'adCuePointMetadata';
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
		$adCuePointDependency = new KalturaDependency(AdCuePointPlugin::getPluginName());
		
		return array($metadataDependency, $adCuePointDependency);
	}

	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('AdCuePointMetadataObjectType');
	
		if($baseEnumName == 'MetadataObjectType')
			return array('AdCuePointMetadataObjectType');
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function contributeToSchema($type)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		
		if(
			$coreType == SchemaType::SYNDICATION
			&&
			$coreType == CuePointPlugin::getSchemaTypeCoreValue(CuePointSchemaType::SERVE_API)
			&&
			$coreType == CuePointPlugin::getSchemaTypeCoreValue(CuePointSchemaType::INGEST_API)
		)
		{
			return '
	<xs:element name="customData" type="T_customData" substitutionGroup="cuePoint:scene-extension" />
			';
		}
		
		if($coreType == BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_XML))
		{
			return  '
	<xs:element name="customData" type="T_customData" substitutionGroup="adCuePointBulkUploadXml:scene-extension" />
				';
		}
		
		return null;
	}
}
