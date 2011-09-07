<?php
/**
 * Enable entry caption asset ingestion from XML bulk upload
 * @package plugins.caption
 */
class CaptionBulkUploadXmlPlugin extends KalturaPlugin implements IKalturaPending, IKalturaSchemaContributor, IKalturaBulkUploadXmlHandler, IKalturaConfigurator
{
	const PLUGIN_NAME = 'captionBulkUploadXml';
	const BULK_UPLOAD_XML_PLUGIN_NAME = 'bulkUploadXml';

	const BULK_UPLOAD_XML_VERSION_MAJOR = 1;
	const BULK_UPLOAD_XML_VERSION_MINOR = 1;
	const BULK_UPLOAD_XML_VERSION_BUILD = 0;
	
	/**
	 * @var BulkUploadEngineXml
	 */
	private $xmlBulkUploadEngine = null;

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
			self::BULK_UPLOAD_XML_VERSION_BUILD
		);

		$captionDependency = new KalturaDependency(CaptionPlugin::getPluginName());
		$bulkUploadXmlDependency = new KalturaDependency(self::BULK_UPLOAD_XML_PLUGIN_NAME, $bulkUploadXmlVersion);

		return array($bulkUploadXmlDependency, $captionDependency);
	}

	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	*/
	public static function contributeToSchema($type)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		if(
			$coreType != BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_XML)
			&&
			$coreType != BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_RESULT_XML)
		)
			return null;

		$xsd = '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:complexType name="T_subTitle">
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
			<xs:element ref="subtitle-extension" minOccurs="0" maxOccurs="unbounded" />
		</xs:sequence>
		
		<xs:attribute name="captionParamsId" type="xs:int" use="optional">
			<xs:annotation>
				<xs:documentation>The asset id to be updated with this resource used only for update</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="captionParams" type="xs:string" use="optional">
			<xs:annotation>
				<xs:documentation>System name of caption params to be associated with the caption asset</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="captionAssetId" type="xs:string" use="optional">
			<xs:annotation>
				<xs:documentation>ID of caption params to be associated with the caption asset</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="isDefault" type="xs:boolean" use="optional">
			<xs:annotation>
				<xs:documentation>Specifies if this asset is the default caption asset</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="format" type="KalturaCaptionType" use="optional">
			<xs:annotation>
				<xs:documentation>Caption asset file format</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="lang" type="KalturaLanguage" use="optional">
			<xs:annotation>
				<xs:documentation>Caption asset file language</xs:documentation>
			</xs:annotation>
		</xs:attribute>
						
	</xs:complexType>
	
	<xs:element name="subtitle-extension" />
	<xs:element name="subTitle" type="T_subTitle" substitutionGroup="item-extension">
		<xs:annotation>
			<xs:documentation>Caption asset element</xs:documentation>
			<xs:appinfo>
				<example>
					<subTitle isDefault="true" format="2" lang="Hebrew">
						<tags>
							<tag>example</tag>
							<tag>my_tag</tag>
						</tags>
						<urlContentResource url="http://my.domain/path/caption.srt"/>
					</subTitle>
				</example>
			</xs:appinfo>
		</xs:annotation>
	</xs:element>
		';

		return $xsd;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaBulkUploadXmlHandler::configureBulkUploadXmlHandler()
	 */
	public function configureBulkUploadXmlHandler(BulkUploadEngineXml $xmlBulkUploadEngine)
	{
		$this->xmlBulkUploadEngine = $xmlBulkUploadEngine;
	}

	/* (non-PHPdoc)
	 * @see IKalturaBulkUploadXmlHandler::handleItemAdded()
	*/
	public function handleItemAdded(KalturaObjectBase $object, SimpleXMLElement $item)
	{
		if(!($object instanceof KalturaBaseEntry))
			return;
		  
		if(empty($item->subTitle))
			return;
		
		$this->xmlBulkUploadEngine->impersonate();
		
		foreach($item->subTitle as $caption)
			$this->handleCaptionAsset($object->id, $object->conversionProfileId, $object->partnerId, $caption);
		
		$this->xmlBulkUploadEngine->unimpersonate();
	}

	private function handleCaptionAsset($entryId, $conversionProfileId, SimpleXMLElement $caption)
	{
		$captionAssetPlugin = KalturaCaptionClientPlugin::get($this->xmlBulkUploadEngine->getClient());
		
		$captionAsset = new KalturaCaptionAsset();
		$captionAsset->flavorParamsId = $this->xmlBulkUploadEngine->getAssetParamsId($caption, $conversionProfileId, true, 'caption');
		$captionAsset->tags = $this->xmlBulkUploadEngine->implodeChildElements($contentElement->tags);
		
		if(isset($caption->captionAssetId))
			$captionAsset->id = $caption->captionAssetId;
		
		if(isset($caption['isDefault']))
		$captionAsset->isDefault = $caption['isDefault'];
		
		if(isset($caption['format']))
			$captionAsset->format = $caption['format'];
		
		if(isset($caption['lang']))
			$captionAsset->language = $caption['lang'];
				
		$captionAssetId = null;
		if(isset($caption['captionAssetId']))
		{
			$captionAssetId = $caption['captionAssetId'];
			$captionAssetPlugin->captionAsset->update($captionAssetId, $captionAsset);
		}
		else
		{
			$captionAsset = $captionAssetPlugin->captionAsset->add($entryId, $captionAsset);
			$captionAssetId = $captionAsset->id;
		}
		
		$captionAssetResource = $this->xmlBulkUploadEngine->getResource($caption, $conversionProfileId);
		if($captionAssetResource)
			$captionAssetPlugin->captionAsset->setContent($captionAssetId, $captionAssetResource);
	}

	/* (non-PHPdoc)
	 * @see IKalturaBulkUploadXmlHandler::handleItemUpdated()
	*/
	public function handleItemUpdated(KalturaObjectBase $object, SimpleXMLElement $item)
	{
		$this->handleItemAdded($client, $object, $item);
	}

	/* (non-PHPdoc)
	 * @see IKalturaBulkUploadXmlHandler::handleItemDeleted()
	*/
	public function handleItemDeleted(KalturaObjectBase $object, SimpleXMLElement $item)
	{
		// No handling required
	}

	/* (non-PHPdoc)
	 * @see IKalturaConfigurator::getConfig()
	*/
	public static function getConfig($configName)
	{
		if($configName == 'generator')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/captionBulkUploadXml.generator.ini');

		return null;
	}
}
