<?php
/**
 * Enable cue point ingestion from XML bulk upload
 * @package plugins.cuePoint
 */
class CuePointBulkUploadXmlPlugin extends KalturaPlugin implements IKalturaPending, IKalturaSchemaContributor
{
	const PLUGIN_NAME = 'cuePointBulkUploadXml';
	const BULK_UPLOAD_XML_PLUGIN_NAME = 'bulkUploadXml';
	
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
		$bulkUploadXmlDependency = new KalturaDependency(self::BULK_UPLOAD_XML_PLUGIN_NAME);
		$cuePointDependency = new KalturaDependency(CuePointPlugin::getPluginName());
		
		return array($bulkUploadXmlDependency, $cuePointDependency);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function contributeToSchema($type)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		if($coreType != BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_XML))
			return null;
	
		$xsd = '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:complexType name="T_scenes">
		<xs:sequence>
			<xs:element ref="scene" minOccurs="1" maxOccurs="unbounded" />
		</xs:sequence>
	</xs:complexType>	

	<xs:complexType name="T_scene">
		<xs:sequence>
			<xs:element name="sceneStartTime" minOccurs="1" maxOccurs="1" type="xs:time" />
			<xs:element name="tags" minOccurs="1" maxOccurs="1" type="T_tags" />
	
			<xs:element ref="scene-extension" minOccurs="0" maxOccurs="unbounded" />
		</xs:sequence>
		
		<xs:attribute name="sceneId" use="required" type="xs:string" />
		<xs:attribute name="systemName" use="optional" type="xs:string" />
		
	</xs:complexType>
	
	<xs:element name="scenes" type="T_scenes" substitutionGroup="item-extension" />
	<xs:element name="scene" type="T_scene" />
	<xs:element name="scene-extension" />
		';
		
		return $xsd;
	}
}
