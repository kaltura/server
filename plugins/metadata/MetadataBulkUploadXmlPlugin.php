<?php
/**
 * Enable custom metadata ingestion from XML bulk upload
 * @package plugins.metadata
 */
class MetadataBulkUploadXmlPlugin extends KalturaPlugin implements IKalturaPending, IKalturaSchemaContributor
{
	const PLUGIN_NAME = 'metadataBulkUploadXml';
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
		$metadataDependency = new KalturaDependency(MetadataPlugin::getPluginName());
		
		return array($bulkUploadXmlDependency, $metadataDependency);
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
	
	<xs:complexType name="T_customData">
		<xs:sequence>
			<xs:any namespace="##local" processContents="skip"/>			
		</xs:sequence>
		
		<xs:attribute name="metadataId" use="optional" type="xs:int">
			<xs:annotation>
				<xs:documentation>Id of custom metadata object to apply update/delete action on</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="metadataProfile" use="optional" type="xs:string">
			<xs:annotation>
				<xs:documentation>Custom metadata schema profile system name</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="metadataProfileId" use="optional" type="xs:int">
			<xs:annotation>
				<xs:documentation>Custom metadata schema profile id</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		
	</xs:complexType>
	
	<xs:element name="customData" type="T_customData" substitutionGroup="item-extension" >
		<xs:annotation>
			<xs:documentation>Custom metadata XML</xs:documentation>
		</xs:annotation>
	</xs:element>
		';
		
		return $xsd;
	}
}
