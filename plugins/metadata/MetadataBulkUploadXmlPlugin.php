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
			<xs:element name="action" minOccurs="0" maxOccurs="1">
				<xs:annotation>
					<xs:documentation>
						The action to apply:<br/>
						transformXslt - transform metadata object using Xslt<br/>
					</xs:documentation>
				</xs:annotation>
				<xs:simpleType>
					<xs:restriction base="xs:string">
						<xs:enumeration value="transformXslt" />
						<xs:enumeration value="replace" />
					</xs:restriction>
				</xs:simpleType>
			</xs:element>
			<xs:element name="xslt" minOccurs="0" maxOccurs="1">
				<xs:annotation>
					<xs:documentation>
						The xslt to transform on the current metadata object
					</xs:documentation>
				</xs:annotation>
			</xs:element>
			<xs:element ref="xmlData" maxOccurs="1" minOccurs="0">
				<xs:annotation>
					<xs:documentation>metadata xml Data</xs:documentation>
				</xs:annotation>
			</xs:element>
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
	
	<xs:complexType name="T_xmlData">
		<xs:sequence>
			<xs:any namespace="##local" processContents="skip" minOccurs="1" maxOccurs="1">
				<xs:annotation>
					<xs:documentation>The XML for custom metadata according to a schema profile</xs:documentation>
				</xs:annotation>		
			</xs:any>
		</xs:sequence>
	</xs:complexType>
	<xs:complexType name="T_customDataItems">
		<xs:sequence>
			<xs:element name="action" minOccurs="0" maxOccurs="1">
				<xs:annotation>
					<xs:documentation>
						The action to apply:<br/>
						Update - Update metadata for existing entry<br/>
					</xs:documentation>
				</xs:annotation>
				<xs:simpleType>
					<xs:restriction base="xs:string">
						<xs:enumeration value="update" />
					</xs:restriction>
				</xs:simpleType>
			</xs:element>
			<xs:element ref="customData" maxOccurs="unbounded" minOccurs="0">
				<xs:annotation>
					<xs:documentation>All custom data elemets</xs:documentation>
				</xs:annotation>
			</xs:element>
		</xs:sequence>
	</xs:complexType>
	
	
	<xs:element name="customDataItems" type="T_customDataItems" substitutionGroup="item-extension">
		<xs:annotation>
			<xs:documentation>All custom metadata elemets</xs:documentation>
			<xs:appinfo>
				<example>
					<customDataItems>
						<action>update</action>
						<customData>...</customData>
						<customData>...</customData>
						<customData>...</customData>
					</customDataItems>
				</example>
			</xs:appinfo>
		</xs:annotation>
	</xs:element>
	
	<xs:element name="customData" type="T_customData">
		<xs:annotation>
			<xs:documentation>XML for custom metadata</xs:documentation>
			<xs:appinfo>
				<example>
					<customData	metadataId="{metadata id}" 
								metadataProfile="MY_METADATA_PROFILE_SYSTEM_NAME}"  
								metadataProfileId="{metadata profile id}"  
					>
						<action>transformXslt</action>
						<xslt></xslt>
						<xmlData>
							<metadata>
								<TextFieldName>entry field value</TextFieldName>
								<MultipleTextFieldName>entry multiple text field value1</MultipleTextFieldName>
								<MultipleTextFieldName>entry multiple text field value2</MultipleTextFieldName>
								<TextSelectionListFieldName>entry selected text value</TextSelectionListFieldName>
								<DateFieldName>21741540</DateFieldName>
								<EntryIDFieldName>0_5b3t2c8z</EntryIDFieldName>
							</metadata>
						</xmlData>
					</customData>
				</example>
			</xs:appinfo>
		</xs:annotation>
	</xs:element>
	
	<xs:element name="xmlData" type="T_xmlData">
		<xs:annotation>
			<xs:documentation>XML data for custom metadata</xs:documentation>
			<xs:appinfo>
				<example>
						<xmlData>
							<metadata>
								<TextFieldName>entry field value</TextFieldName>
								<MultipleTextFieldName>entry multiple text field value1</MultipleTextFieldName>
								<MultipleTextFieldName>entry multiple text field value2</MultipleTextFieldName>
								<TextSelectionListFieldName>entry selected text value</TextSelectionListFieldName>
								<DateFieldName>21741540</DateFieldName>
								<EntryIDFieldName>0_5b3t2c8z</EntryIDFieldName>
							</metadata>
						</xmlData>
				</example>
			</xs:appinfo>
		</xs:annotation>
	</xs:element>
	
	<xs:complexType name="T_metadataReplacementOptionsItem">
		<xs:complexContent>
				<xs:extension base="T_pluginReplacementOptionsItem">
					<xs:sequence>
						<xs:element name="shouldCopyMetadata" minOccurs="0" maxOccurs="1">
							<xs:simpleType>
								<xs:restriction base="xs:string">
									<xs:enumeration value="true"/>
									<xs:enumeration value="false"/>
								</xs:restriction>
							</xs:simpleType>
						</xs:element>
					</xs:sequence>
			</xs:extension>
		</xs:complexContent>
	</xs:complexType>
	
	<xs:complexType name="T_metadataReplacementOptions">
		<xs:complexContent>
			<xs:extension base="T_pluginReplacementOptions">
				<xs:sequence>
					<xs:element name="metadataReplacementOptionsItem" type="T_metadataReplacementOptionsItem" minOccurs="0" maxOccurs="1">
						<xs:annotation>
							<xs:appinfo>
								<example>
								</example>
							</xs:appinfo>
						</xs:annotation>
					</xs:element>
				</xs:sequence>
			</xs:extension>
		</xs:complexContent>
	</xs:complexType>	
	
	<xs:element name="pluginReplacementOptions" type="T_metadataReplacementOptions" substitutionGroup="item-extension">
		<xs:annotation>
			<xs:appinfo>
				<example>
					<pluginReplacementOptions>
						<pluginReplacementOptionsItem>
						...
						</pluginReplacementOptionsItem>
						<pluginReplacementOptionsItem>
						...
						</pluginReplacementOptionsItem>
						...
						...
					</pluginReplacementOptions>
				</example>
			</xs:appinfo>
		</xs:annotation>
	</xs:element>
		';
		
		return $xsd;
	}
}
