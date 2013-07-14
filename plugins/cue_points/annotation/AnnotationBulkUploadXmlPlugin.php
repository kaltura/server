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
		if(
			$coreType != BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_XML)
			&&
			$coreType != BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_RESULT_XML)
		)
			return null;
	
		$xsd = '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:complexType name="T_scene_annotationBulkUploadXml">
		<xs:complexContent>
			<xs:extension base="T_scene">
				<xs:sequence>
					<xs:element name="sceneEndTime" minOccurs="1" maxOccurs="1" type="xs:time">
						<xs:annotation>
							<xs:documentation>A cue point that marks the end time</xs:documentation>
						</xs:annotation>
					</xs:element>
					<xs:element name="sceneText" minOccurs="0" maxOccurs="1" type="xs:string">
						<xs:annotation>
							<xs:documentation>A free text description</xs:documentation>
						</xs:annotation>
					</xs:element>
					<xs:choice minOccurs="0" maxOccurs="1">
						<xs:element name="parent" minOccurs="1" maxOccurs="1">
							<xs:annotation>
								<xs:documentation>The system name of a parent annotation</xs:documentation>
							</xs:annotation>
							<xs:simpleType>
								<xs:restriction base="xs:string">
									<xs:maxLength value="120"/>
								</xs:restriction>
							</xs:simpleType>
						</xs:element>
						<xs:element name="parentId" minOccurs="1" maxOccurs="1">
							<xs:annotation>
								<xs:documentation>The identifier of a parent annotation</xs:documentation>
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
			<xs:documentation>A single annotation cue point element</xs:documentation>
			<xs:appinfo>
				<example title="XML Example 1: Single annotation">
					<scene-annotation entryId="{entry id}">
						<sceneStartTime>00:00:05.3</sceneStartTime>
						<tags>
							<tag>tag1</tag>
							<tag>tag2</tag>
						</tags>
						<sceneEndTime>00:00:10</sceneEndTime>
						<sceneText>my annotation</sceneText>
					</scene-annotation>
				</example>
				<example title="Example 2: Multiple related annotations">
					<scene-annotation entryId="{entry id}" systemName="MY_ANNOTATION_PARENT_SYSTEM_NAME">
						<sceneStartTime>00:00:05.3</sceneStartTime>
						<tags>
							<tag>tag1</tag>
							<tag>tag2</tag>
						</tags>
						<sceneEndTime>00:00:10</sceneEndTime>
						<sceneText>my annotation parent</sceneText>
					</scene-annotation>
					<scene-annotation entryId="{entry id}">
						<sceneStartTime>00:00:05.3</sceneStartTime>
						<tags>
							<tag>tag3</tag>
							<tag>tag4</tag>
						</tags>
						<sceneEndTime>00:00:10</sceneEndTime>
						<sceneText>my annotation child</sceneText>
						<parent>MY_ANNOTATION_PARENT_SYSTEM_NAME</parent>
					</scene-annotation>
				</example>
			</xs:appinfo>
		</xs:annotation>
	</xs:element>
		';
		
		return $xsd;
	}
}
