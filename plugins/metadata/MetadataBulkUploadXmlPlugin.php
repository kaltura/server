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
		if(
			$coreType != BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_XML)
			&&
			$coreType != BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_RESULT_XML)
		)
			return null;
	
		$xsd = '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:complexType name="T_customData">
		<xs:sequence>
			<xs:any namespace="##local" processContents="skip" minOccurs="1" maxOccurs="1">
				<xs:annotation>
					<xs:documentation>The XML for custom metadata according to a schema profile</xs:documentation>
				</xs:annotation>		
			</xs:any>
		</xs:sequence>
		
		<xs:attribute name="metadataId" use="optional" type="xs:int">
			<xs:annotation>
				<xs:documentation>The identifier of the custom metadata object that an update/delete action applies to</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="metadataProfile" use="optional">
			<xs:annotation>
				<xs:documentation>The schema profile system name of the custom metadata</xs:documentation>
			</xs:annotation>
			<xs:simpleType>
				<xs:restriction base="xs:string">
					<xs:maxLength value="120"/>
				</xs:restriction>
			</xs:simpleType>
		</xs:attribute>
		<xs:attribute name="metadataProfileId" use="optional" type="xs:int">
			<xs:annotation>
				<xs:documentation>The schema profile identifier of the custom metadata</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		
	</xs:complexType>
	
	<xs:element name="customData" type="T_customData" substitutionGroup="item-extension">
		<xs:annotation>
			<xs:documentation>XML for custom metadata</xs:documentation>
			<xs:appinfo>
				<example>
					<customData	metadataId="{metadata id}" 
								metadataProfile="MY_METADATA_PROFILE_SYSTEM_NAME}"  
								metadataProfileId="{metadata profile id}"  
					>
						<metadata>
							<TextFieldName>entry field value</TextFieldName>
							<MultipleTextFieldName>entry multiple text field value1</MultipleTextFieldName>
							<MultipleTextFieldName>entry multiple text field value2</MultipleTextFieldName>
							<TextSelectionListFieldName>entry selected text value</TextSelectionListFieldName>
							<DateFieldName>21741540<DateFieldName>
							<EntryIDFieldName>0_5b3t2c8z</EntryIDFieldName>
						</metadata>
					</customData>
				</example>
			</xs:appinfo>
		</xs:annotation>
	</xs:element>
		';
		
		return $xsd;
	}
}
