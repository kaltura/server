<?php
/**
 * Enable code cue point ingestion from XML bulk upload
 * @package plugins.codeCuePoint
 */
class CodeCuePointBulkUploadXmlPlugin extends KalturaPlugin implements IKalturaPending, IKalturaSchemaContributor
{
	const PLUGIN_NAME = 'codeCuePointBulkUploadXml';
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
		$codeCuePointDependency = new KalturaDependency(CodeCuePointPlugin::getPluginName());
		
		return array($bulkUploadXmlDependency, $codeCuePointDependency);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::isContributingToSchema()
	 */
	public static function isContributingToSchema($type)
	{
		return ($type == BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_XML));  
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function contributeToSchema($type, SimpleXMLElement $xsd)
	{
		if($type != BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_XML))
			return;
	
		$import = $xsd->addChild('import');
		$import->addAttribute('schemaLocation', 'http://' . kConf::get('cdn_host') . "/api_v3/service/schema/action/serve/type/$type/name/" . self::getPluginName());
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function getPluginSchema($type)
	{
		if($type != BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_XML))
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
								<xs:element name="code" minOccurs="0" maxOccurs="1" type="xs:string" />
								<xs:element name="description" minOccurs="0" maxOccurs="1" type="xs:string" />
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
