<?php
/**
 * Enable code cue point ingestion from XML bulk upload
 * @package plugins.codeCuePoint
 */
class CodeCuePointBulkUploadXmlPlugin extends KalturaPlugin implements IKalturaPending, IKalturaSchemaContributor
{
	const PLUGIN_NAME = 'codeCuePointBulkUploadXml';
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
			return CodeCuePointBulkUploadXmlHandler::get();
			
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
		$codeCuePointDependency = new KalturaDependency(CodeCuePointPlugin::getPluginName());
		
		return array($bulkUploadXmlDependency, $codeCuePointDependency);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function contributeToSchema($type)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		if($coreType != BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_XML))
			return null;
	
		$xmlnsBase = "http://" . kConf::get('www_host') . "/$type";
		$xmlnsPlugin = "http://" . kConf::get('www_host') . "/$type/" . self::getPluginName();
		
		$xsd = '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:complexType name="T_scene_codeCuePointBulkUploadXml">
		<xs:complexContent>
			<xs:extension base="T_scene">
				<xs:sequence>
					<xs:element name="code" minOccurs="0" maxOccurs="1" type="xs:string" />
					<xs:element name="description" minOccurs="0" maxOccurs="1" type="xs:string" />
					
					<xs:element ref="scene-extension" minOccurs="0" maxOccurs="unbounded" />
				</xs:sequence>
			</xs:extension>
		</xs:complexContent>
	</xs:complexType>
	
	<xs:element name="scene-code-cue-point" type="T_scene_codeCuePointBulkUploadXml" substitutionGroup="scene" />
		';
		
		return $xsd;
	}
}
