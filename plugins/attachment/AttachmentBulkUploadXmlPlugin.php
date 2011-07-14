<?php
/**
 * Enable entry attachment asset ingestion from XML bulk upload
 * @package plugins.attachment
 */
class AttachmentBulkUploadXmlPlugin extends KalturaPlugin implements IKalturaPending, IKalturaSchemaContributor
{
	const PLUGIN_NAME = 'attachmentBulkUploadXml';
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
		$attachmentDependency = new KalturaDependency(AttachmentPlugin::getPluginName());
		
		return array($bulkUploadXmlDependency, $attachmentDependency);
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
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:complexType name="T_subTitle">
		<xs:sequence>
			<xs:element name="tags" minOccurs="1" maxOccurs="1" type="T_tags" />
			<xs:choice minOccurs="1" maxOccurs="1">
				<xs:element ref="serverFileContentResource" minOccurs="1" maxOccurs="1" />
				<xs:element ref="urlContentResource" minOccurs="1" maxOccurs="1" />
				<xs:element ref="remoteStorageContentResource" minOccurs="1" maxOccurs="1" />
				<xs:element ref="entryContentResource" minOccurs="1" maxOccurs="1" />
				<xs:element ref="assetContentResource" minOccurs="1" maxOccurs="1" />
			</xs:choice>
			<xs:element name="filename" minOccurs="0" maxOccurs="1" type="xs:string" />
			<xs:element name="title" minOccurs="0" maxOccurs="1" type="xs:string" />
			<xs:element name="description" minOccurs="0" maxOccurs="1" type="xs:string" />
			<xs:element ref="subtitle-extension" minOccurs="0" maxOccurs="unbounded" />
		</xs:sequence>
		
		<xs:attribute name="attachmentAssetId" type="xs:string" use="optional" />
		<xs:attribute name="format" type="KalturaAttachmentType" use="optional" />
		<xs:attribute name="href" type="xs:string" use="optional" />
						
	</xs:complexType>
	
	<xs:element name="subtitle-extension" />
	<xs:element name="subTitle" type="T_subTitle" substitutionGroup="item-extension" />
		';
		
		return $xsd;
	}
}
