<?php
/**
 * Enable thumb cue point ingestion from XML bulk upload
 * @package plugins.thumbCuePoint
 */
class ThumbCuePointBulkUploadXmlPlugin extends KalturaPlugin implements IKalturaPending, IKalturaSchemaContributor
{
	const PLUGIN_NAME = 'ThumbCuePointBulkUploadXml';
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
			return ThumbCuePointBulkUploadXmlHandler::get();
			
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
		$thumbCuePointDependency = new KalturaDependency(ThumbCuePointPlugin::getPluginName());
		
		return array($bulkUploadXmlDependency, $thumbCuePointDependency);
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
	
		$xmlnsBase = "http://" . kConf::get('www_host') . "/$type";
		$xmlnsPlugin = "http://" . kConf::get('www_host') . "/$type/" . self::getPluginName();
		
		$xsd = '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:complexType name="T_scene_thumbCuePoint">
		<xs:complexContent>
			<xs:extension base="T_scene">
				<xs:sequence>
					<xs:element name="title" minOccurs="1" maxOccurs="1" type="xs:string"> </xs:element>
					<xs:element name="description" minOccurs="1" maxOccurs="1" type="xs:string"> </xs:element>
					<xs:element maxOccurs="1" minOccurs="1" ref="slide" />
					<xs:element ref="scene-extension" minOccurs="0" maxOccurs="unbounded" />
				</xs:sequence>
			</xs:extension>
		</xs:complexContent>
	</xs:complexType>
	
	<xs:complexType name="T_slide">
		<xs:sequence>
			<xs:choice maxOccurs="1" minOccurs="0">
				<xs:element maxOccurs="1" minOccurs="0" ref="urlContentResource"></xs:element>
				<xs:element maxOccurs="1" minOccurs="0" ref="remoteStorageContentResource"></xs:element>
				<xs:element maxOccurs="1" minOccurs="0" ref="remoteStorageContentResources"></xs:element>
				<xs:element maxOccurs="1" minOccurs="0" ref="assetContentResource"></xs:element>
				<xs:element maxOccurs="1" minOccurs="0" ref="entryContentResource"></xs:element>
				<xs:element maxOccurs="1" minOccurs="0" ref="contentResource-extension"></xs:element>
			</xs:choice>
		</xs:sequence>
		<xs:attribute name="timedThumbAssetId" type="xs:string" use="optional"/>
	</xs:complexType>
	
	<xs:element name="scene-thumb-cue-point" type="T_scene_thumbCuePoint" substitutionGroup="scene">
		<xs:annotation>
			<xs:documentation>Single thumb cue point element</xs:documentation>
			<xs:appinfo>
				<example>
					<scene-thumb-cue-point sceneId="{scene id}" entryId="{entry id}">
						<sceneStartTime>00:00:05.3</sceneStartTime>
						<tags>
							<tag>my_tag</tag>
						</tags>
						<slide>
							<urlContentResource url="URL_TO_FILE"/>
						</slide>
					</scene-thumb-cue-point>
				</example>
			</xs:appinfo>
		</xs:annotation>
	</xs:element>
	
	<xs:element name="slide" type="T_slide">
		<xs:annotation>
			<xs:documentation>
				The slide image to attahce to tht thumb cue point ellement
			</xs:documentation>
		</xs:annotation>
	</xs:element>
		';
		
		return $xsd;
	}
}