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
			<xs:element ref="scene" minOccurs="1" maxOccurs="unbounded">
				<xs:annotation>
					<xs:documentation>Cue point element</xs:documentation>
				</xs:annotation>
			</xs:element>
		</xs:sequence>
	</xs:complexType>	

	<xs:complexType name="T_scene" abstract="true">
		<xs:sequence>
			<xs:element name="sceneStartTime" minOccurs="1" maxOccurs="1" type="xs:time">
				<xs:annotation>
					<xs:documentation>Cue point start time</xs:documentation>
				</xs:annotation>
			</xs:element>
			<xs:element ref="tags" minOccurs="1" maxOccurs="1">
				<xs:annotation>
					<xs:documentation>Cue point searchable keywords</xs:documentation>
				</xs:annotation>
			</xs:element>
		</xs:sequence>
		
		<xs:attribute name="sceneId" use="optional" type="xs:string">
			<xs:annotation>
				<xs:documentation>ID of cue point to apply update/delete action on</xs:documentation>
			</xs:annotation>
			<xs:simpleType>
				<xs:restriction base="xs:string">
					<xs:maxLength value="250"/>
				</xs:restriction>
			</xs:simpleType>
		</xs:attribute>
		<xs:attribute name="systemName" use="optional" type="xs:string">
			<xs:annotation>
				<xs:documentation>System name of cue point to apply update/delete action on</xs:documentation>
			</xs:annotation>
			<xs:simpleType>
				<xs:restriction base="xs:string">
					<xs:maxLength value="120"/>
				</xs:restriction>
			</xs:simpleType>
		</xs:attribute>
		
	</xs:complexType>
	
	<xs:element name="scenes" type="T_scenes" substitutionGroup="item-extension">
		<xs:annotation>
			<xs:documentation>Cue points wrapper</xs:documentation>
		</xs:annotation>
	</xs:element>
	
	<xs:element name="scene" type="T_scene">
		<xs:annotation>
			<xs:documentation>
				Base cue point element<br/>
				Is abstract and cannot be used<br/>
				Use the extended elements only
			</xs:documentation>
		</xs:annotation>
	</xs:element>
	
	<xs:element name="scene-extension" />
		';
		
		return $xsd;
	}
}
