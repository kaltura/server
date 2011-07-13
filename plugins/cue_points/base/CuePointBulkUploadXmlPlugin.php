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
				<xs:complexType name="T_scenes">
					<xs:sequence>
						<xs:element ref="scene" minOccurs="1" maxOccurs="unbounded" />
					</xs:sequence>
				</xs:complexType>	
			
				<xs:complexType name="T_scene">
					<xs:sequence>
						<xs:element name="sceneStartTime" minOccurs="1" maxOccurs="1" type="xs:time" />
						<xs:element name="tags" minOccurs="1" maxOccurs="1" type="core:tags" />
				
						<xs:element ref="scene-extension" minOccurs="0" maxOccurs="unbounded" />
					</sequence>
					
					<xs:attribute name="sceneId" use="required" type="xs:int" />
					<xs:attribute name="systemName" use="optional" type="xs:string" />
					
				</complexType>
				
				<xs:element name="scenes" type="T_scenes" substitutionGroup="core:item-extension" />
				<xs:element name="scene" type="T_scene" />
				<xs:element name="scene-extension" />
			</xs:schema>
		';
		
		return new SimpleXMLElement($xsd);
	}
}
