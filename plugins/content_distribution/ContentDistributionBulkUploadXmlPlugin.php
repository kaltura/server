<?php
/**
 * Enable entry content distribution ingestion from XML bulk upload
 * @package plugins.contentDistribution
 */
class ContentDistributionBulkUploadXmlPlugin extends KalturaPlugin implements IKalturaPending, IKalturaSchemaContributor
{
	const PLUGIN_NAME = 'contentDistributionBulkUploadXml';
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
		$contentDistributionDependency = new KalturaDependency(ContentDistributionPlugin::getPluginName());
		
		return array($bulkUploadXmlDependency, $contentDistributionDependency);
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
				
				<xs:complexType name="T_distribution">
					<xs:sequence>
						<xs:choice minOccurs="1" maxOccurs="1">
							<xs:element name="distributionProvider" minOccurs="1" maxOccurs="1" type="enums:KalturaDistributionProviderType" />
							<xs:element name="distributionProfileId" minOccurs="1" maxOccurs="1" type="xs:int" />
							<xs:element name="distributionProfile" minOccurs="1" maxOccurs="1" type="xs:string" />
						</xs:choice>
						<xs:element name="sunrise" minOccurs="0" maxOccurs="1" type="xs:dateTime">
							<xs:annotation>
								<xs:documentation>
									Taken from the entry if not specified.
								</xs:documentation>
							</xs:annotation>
						</xs:element>
						<xs:element name="sunset" minOccurs="0" maxOccurs="1" type="xs:dateTime">
							<xs:annotation>
								<xs:documentation>
									Taken from the entry if not specified.
								</xs:documentation>
							</xs:annotation>
						</xs:element>
						<xs:element name="flavorAssetIds" minOccurs="0" maxOccurs="1" type="xs:string">
							<xs:annotation>
								<xs:documentation>
									List of existing flavor asset ids to be used in this distribution destination.
									Could be used only on existing entry.
								</xs:documentation>
							</xs:annotation>
						</xs:element>
						<xs:element name="thumbAssetIds" minOccurs="0" maxOccurs="1" type="xs:string">
							<xs:annotation>
								<xs:documentation>
									List of existing thumbnail asset ids to be used in this distribution destination.
									Could be used only on existing entry.
								</xs:documentation>
							</xs:annotation>
						</xs:element>
					
						<xs:element ref="distribution-extension" minOccurs="0" maxOccurs="unbounded" />
						
					</xs:sequence>
					
					<xs:attribute name="entryDistributionId" use="required" type="xs:int" />
					<xs:attribute name="submitWhenReady" use="optional" type="xs:boolean" default="false" />
					
				</xs:complexType>
				
				<xs:element name="distribution" type="T_distribution" substitutionGroup="core:item-extension" />
				<xs:element name="distribution-extension" />
			</xs:schema>
		';
		
		return new SimpleXMLElement($xsd);
	}
}
