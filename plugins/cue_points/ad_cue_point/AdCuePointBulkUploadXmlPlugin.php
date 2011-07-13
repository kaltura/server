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
	 * @see IKalturaSchemaContributor::isContributingToSchema()
	 */
	public static function isContributingToSchema($type)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		return ($coreType == BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_XML));  
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function contributeToSchema($type, SimpleXMLElement $xsd)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		if($coreType != BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_XML))
			return;
	
		$import = $xsd->addChild('import');
		$import->addAttribute('schemaLocation', 'http://' . kConf::get('cdn_host') . "/api_v3/service/schema/action/serve/type/$type/name/" . self::getPluginName());
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function getPluginSchema($type)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		if($coreType != BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_XML))
			return null;
	
		$xmlnsBase = "http://" . kConf::get('www_host') . "/$type";
		$xmlnsPlugin = "http://" . kConf::get('www_host') . "/$type/" . self::getPluginName();
		
		$xsd = '<?xml version="1.0" encoding="UTF-8"?>
			<xs:schema 
				xmlns:xs="http://www.w3.org/2001/XMLSchema"
				xmlns="' . $xmlnsPlugin . '" 
				xmlns:core="' . $xmlnsBase . '" 
				targetNamespace="' . $xmlnsPlugin . '"
			>
				<xs:complexType name="T_scene">
					<xs:complexContent>
						<xs:extension base="cuePoint:T_scene">
							<xs:sequence>
								<xs:element name="sceneEndTime" minOccurs="1" maxOccurs="1" type="xs:time" />
								<xs:element name="sceneTitle" minOccurs="0" maxOccurs="1" type="xs:string" />
								<xs:element name="sourceUrl" minOccurs="0" maxOccurs="1" type="xs:string" />
								<xs:element name="adType" minOccurs="0" maxOccurs="1" type="enum:KalturaAdType" />
								<xs:element name="protocolType" minOccurs="0" maxOccurs="1" type="enum:KalturaAdProtocolType" />
							</xs:sequence>
						</xs:extension>
					</xs:complexContent>
				</xs:complexType>
				
				<xs:element name="scene" type="T_scene" substitutionGroup="cuePoint:scene" />
			</xs:schema>
		';
		
		return new SimpleXMLElement($xsd);
	}
}
