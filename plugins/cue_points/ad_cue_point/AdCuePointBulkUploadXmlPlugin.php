<?php
/**
 * Enable ad cue point ingestion from XML bulk upload
 * @package plugins.adCuePoint
 */
class AdCuePointBulkUploadXmlPlugin extends KalturaPlugin implements IKalturaPending, IKalturaSchemaContributor
{
	const PLUGIN_NAME = 'adCuePointBulkUploadXml';
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
		$adCuePointDependency = new KalturaDependency(AdCuePointPlugin::getPluginName());
		
		return array($bulkUploadXmlDependency, $adCuePointDependency);
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
	<xs:complexType name="T_scene">
		<xs:complexContent>
			<xs:extension base="T_scene">
				<xs:sequence>
					<xs:element name="sceneEndTime" minOccurs="1" maxOccurs="1" type="xs:time" />
					<xs:element name="sceneTitle" minOccurs="0" maxOccurs="1" type="xs:string" />
					<xs:element name="sourceUrl" minOccurs="0" maxOccurs="1" type="xs:string" />
					<xs:element name="adType" minOccurs="0" maxOccurs="1" type="KalturaAdType" />
					<xs:element name="protocolType" minOccurs="0" maxOccurs="1" type="KalturaAdProtocolType" />
				</xs:sequence>
			</xs:extension>
		</xs:complexContent>
	</xs:complexType>
	
	<xs:element name="scene" type="T_scene" substitutionGroup="scene" />
		';
		
		return new SimpleXMLElement($xsd);
	}
}
