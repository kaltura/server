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
		// TODO add IKalturaBulkUploadXmlHandler to handle attachments
		return null;
		
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		if(
			$coreType != BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_XML)
			&&
			$coreType != BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_RESULT_XML)
		)
			return null;
	
		$xsd = '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:complexType name="T_attachment">
		<xs:sequence>
			<xs:element name="tags" minOccurs="1" maxOccurs="1" type="T_tags">
				<xs:annotation>
					<xs:documentation>Specifies specific tags you want to set for the flavor asset</xs:documentation>
				</xs:annotation>
			</xs:element>
			<xs:choice minOccurs="1" maxOccurs="1">
				<xs:element ref="serverFileContentResource" minOccurs="1" maxOccurs="1">
					<xs:annotation>
						<xs:documentation>Specifies that content ingestion location is on a Kaltura hosted server</xs:documentation>
					</xs:annotation>
				</xs:element>
				<xs:element ref="urlContentResource" minOccurs="1" maxOccurs="1">
					<xs:annotation>
						<xs:documentation>Specifies that content file location is a URL (http,ftp)</xs:documentation>
					</xs:annotation>
				</xs:element>
				<xs:element ref="remoteStorageContentResource" minOccurs="1" maxOccurs="1">
					<xs:annotation>
						<xs:documentation>Specifies that content file location is a path within a Kaltura defined remote storage</xs:documentation>
					</xs:annotation>
				</xs:element>
				<xs:element ref="remoteStorageContentResources" minOccurs="1" maxOccurs="1">
					<xs:annotation>
						<xs:documentation>Set of content files within several Kaltura defined remote storages</xs:documentation>
					</xs:annotation>
				</xs:element>
				<xs:element ref="entryContentResource" minOccurs="1" maxOccurs="1">
					<xs:annotation>
						<xs:documentation>Specifies that content is a Kaltura entry</xs:documentation>
					</xs:annotation>
				</xs:element>
				<xs:element ref="assetContentResource" minOccurs="1" maxOccurs="1">
					<xs:annotation>
						<xs:documentation>Specifies that content is a Kaltura asset</xs:documentation>
					</xs:annotation>
				</xs:element>
				<xs:element ref="contentResource-extension" minOccurs="1" maxOccurs="1" />
			</xs:choice>
			<xs:element name="filename" minOccurs="0" maxOccurs="1" type="xs:string">
				<xs:annotation>
					<xs:documentation>Attachment asset file name</xs:documentation>
				</xs:annotation>
			</xs:element>
			<xs:element name="title" minOccurs="0" maxOccurs="1" type="xs:string">
				<xs:annotation>
					<xs:documentation>Attachment asset title</xs:documentation>
				</xs:annotation>
			</xs:element>
			<xs:element name="description" minOccurs="0" maxOccurs="1" type="xs:string">
				<xs:annotation>
					<xs:documentation>Attachment asset free text description</xs:documentation>
				</xs:annotation>
			</xs:element>
			<xs:element ref="attachment-extension" minOccurs="0" maxOccurs="unbounded" />
		</xs:sequence>
		
		<xs:attribute name="attachmentAssetId" type="xs:string" use="optional">
			<xs:annotation>
				<xs:documentation>The asset id to be updated with this resource used only for update</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="format" type="KalturaAttachmentType" use="optional">
			<xs:annotation>
				<xs:documentation>Attachment asset file format</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="href" type="xs:string" use="optional">
			<xs:annotation>
				<xs:documentation>Attachment asset file download URL</xs:documentation>
			</xs:annotation>
		</xs:attribute>
						
	</xs:complexType>
	
	<xs:element name="attachment-extension" />
	<xs:element name="attachment" type="T_attachment" substitutionGroup="item-extension">
		<xs:annotation>
			<xs:documentation>Attachment asset element</xs:documentation>
		</xs:annotation>
	</xs:element>
		';
		
		return $xsd;
	}
}
