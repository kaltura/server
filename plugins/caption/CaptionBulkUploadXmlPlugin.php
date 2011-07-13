<?php
/**
 * Enable entry caption asset ingestion from XML bulk upload
 * @package plugins.caption
 */
class CaptionBulkUploadXmlPlugin extends KalturaPlugin implements IKalturaPending, IKalturaSchemaContributor
{
	const PLUGIN_NAME = 'captionBulkUploadXml';
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
		$captionDependency = new KalturaDependency(CaptionPlugin::getPluginName());
		
		return array($bulkUploadXmlDependency, $captionDependency);
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
			
				<xs:complexType name="T_subTitle">
					<xs:sequence>
						<xs:element name="tags" minOccurs="1" maxOccurs="1" type="core:tags" />
						<xs:choice minOccurs="1" maxOccurs="1">
							<xs:element ref="core:serverFileContentResource" minOccurs="1" maxOccurs="1" />
							<xs:element ref="core:urlContentResource" minOccurs="1" maxOccurs="1" />
							<xs:element ref="core:remoteStorageContentResource" minOccurs="1" maxOccurs="1" />
							<xs:element ref="core:entryContentResource" minOccurs="1" maxOccurs="1" />
							<xs:element ref="core:assetContentResource" minOccurs="1" maxOccurs="1" />
						</xs:choice>
						<xs:element ref="subtitle-extension" minOccurs="0" maxOccurs="unbounded" />
					</xs:sequence>
					
					<xs:attribute name="captionParamsId" type="xs:int" use="optional" />
					<xs:attribute name="captionParams" type="xs:string" use="optional" />
					<xs:attribute name="captionAssetId" type="xs:string" use="optional" />
					<xs:attribute name="isDefault" type="xs:boolean" use="optional" />
					<xs:attribute name="format" type="enums:KalturaCaptionType" use="optional" />
					<xs:attribute name="lang" type="enums:KalturaLanguage" use="optional" />
					<xs:attribute name="href" type="xs:string" use="optional" />
									
				</xs:complexType>
				
				<xs:element name="subtitle-extension" />
				<xs:element name="subTitle" type="T_subTitle" substitutionGroup="core:item-extension" />
			</xs:schema>
		';
		
		return new SimpleXMLElement($xsd);
	}
}
