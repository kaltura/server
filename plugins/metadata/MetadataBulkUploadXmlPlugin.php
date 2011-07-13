<?php
/**
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
				
				<xs:complexType name="T_customData">
					<xs:sequence>
						<xs:any namespace="##local" processContents="skip"/>			
					</xs:sequence>
					
					<attribute name="metadataId" use="optional" type="int"/>
					<attribute name="metadataProfile" use="optional" type="string"/>
					<attribute name="metadataProfileId" use="optional" type="int"/>
					
				</xs:complexType>
				
				<xs:element name="customData" type="T_customData" substitutionGroup="core:item-extension" />
			</xs:schema>
		';
		
		return new SimpleXMLElement($xsd);
	}
}
