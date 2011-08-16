<?php
/**
 * Enable annotation ingestion from XML bulk upload
 * @package plugins.annotation
 */
class AnnotationBulkUploadXmlPlugin extends KalturaPlugin implements IKalturaPending, IKalturaSchemaContributor
{
	const PLUGIN_NAME = 'annotationBulkUploadXml';
	const BULK_UPLOAD_XML_PLUGIN_NAME = 'bulkUploadXml';
	
	const BULK_UPLOAD_XML_VERSION_MAJOR = 1;
	const BULK_UPLOAD_XML_VERSION_MINOR = 1;
	const BULK_UPLOAD_XML_VERSION_BUILD = 0;

	/* (non-PHPdoc)
	 * @see KalturaPlugin::getInstance()
	 */
	public function getInstance($interface)
	{
		if($this instanceof $interface)
			return $this;
			
		if($interface == 'IKalturaBulkUploadXmlHandler')
			return AnnotationBulkUploadXmlHandler::get();
			
		return null;
	}
	
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
		$bulkUploadXmlVersion = new KalturaVersion(
			self::BULK_UPLOAD_XML_VERSION_MAJOR,
			self::BULK_UPLOAD_XML_VERSION_MINOR,
			self::BULK_UPLOAD_XML_VERSION_BUILD);
			
		$bulkUploadXmlDependency = new KalturaDependency(self::BULK_UPLOAD_XML_PLUGIN_NAME, $bulkUploadXmlVersion);
		$annotationDependency = new KalturaDependency(AnnotationPlugin::getPluginName());
		
		return array($bulkUploadXmlDependency, $annotationDependency);
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
	
	<xs:complexType name="T_scene_annotationBulkUploadXml">
		<xs:complexContent>
			<xs:extension base="T_scene">
				<xs:sequence>
					<xs:element name="sceneEndTime" minOccurs="1" maxOccurs="1" type="xs:time">
						<xs:annotation>
							<xs:documentation>Cue point end time</xs:documentation>
						</xs:annotation>
					</xs:element>
					<xs:element name="sceneText" minOccurs="0" maxOccurs="1" type="xs:string">
						<xs:annotation>
							<xs:documentation>Free text description</xs:documentation>
						</xs:annotation>
					</xs:element>
					<xs:choice minOccurs="0" maxOccurs="1">
						<xs:element name="parent" minOccurs="1" maxOccurs="1">
							<xs:annotation>
								<xs:documentation>System name of the parent annotation</xs:documentation>
							</xs:annotation>
							<xs:simpleType>
								<xs:restriction base="xs:string">
									<xs:maxLength value="120"/>
								</xs:restriction>
							</xs:simpleType>
						</xs:element>
						<xs:element name="parentId" minOccurs="1" maxOccurs="1">
							<xs:annotation>
								<xs:documentation>ID of the parent annotation</xs:documentation>
							</xs:annotation>
							<xs:simpleType>
								<xs:restriction base="xs:string">
									<xs:maxLength value="250"/>
								</xs:restriction>
							</xs:simpleType>
						</xs:element>
					</xs:choice>
					
					<xs:element ref="scene-extension" minOccurs="0" maxOccurs="unbounded" />
				</xs:sequence>
			</xs:extension>
		</xs:complexContent>
	</xs:complexType>
	
	<xs:element name="scene-annotation" type="T_scene_annotationBulkUploadXml" substitutionGroup="scene">
		<xs:annotation>
			<xs:documentation>Single annotation element</xs:documentation>
		</xs:annotation>
	</xs:element>
		';
		
		return $xsd;
	}
}
