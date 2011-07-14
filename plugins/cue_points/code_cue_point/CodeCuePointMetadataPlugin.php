<?php
/**
 * Enable custom metadata on code cue point objects
 * @package plugins.codeCuePoint
 */
class CodeCuePointMetadataPlugin extends KalturaPlugin implements IKalturaPending, IKalturaSchemaContributor, IKalturaEnumerator
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
		
		return array($metadataDependency, $codeCuePointDependency);
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
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function contributeToSchema($type)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		if(
			$coreType != SchemaType::SYNDICATION
			&&
			$coreType != CuePointPlugin::getSchemaTypeCoreValue(CuePointSchemaType::SERVE_API)
			&&
			$coreType != CuePointPlugin::getSchemaTypeCoreValue(CuePointSchemaType::INGEST_API)
			&&
			$coreType != BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_XML)
		)
			return null;
	
		$xsd = '
	<xs:complexType name="T_customData">
		<xs:complexContent>
			<xs:extension base="T_customData" />
		</xs:complexContent>
	</xs:complexType>
	
	<xs:element name="customData" type="T_customData" substitutionGroup="scene-extension" />
		';
		
		return $xsd;
	}
}
