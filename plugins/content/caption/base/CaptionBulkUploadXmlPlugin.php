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
	
	/**
	 * @var array
	 */
	protected $currentCaptionAssets = null;

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
	
		<xs:complexType name="T_subTitles">
		<xs:sequence>
			<xs:element name="action" minOccurs="0" maxOccurs="1">
				<xs:annotation>
					<xs:documentation>
						The action to apply:<br/>
						Update - Update existing subtitles<br/>
					</xs:documentation>
				</xs:annotation>
				<xs:simpleType>
					<xs:restriction base="xs:string">
						<xs:enumeration value="update" />
					</xs:restriction>
				</xs:simpleType>
			</xs:element>
			<xs:element ref="subTitle" maxOccurs="unbounded" minOccurs="0">
				<xs:annotation>
					<xs:documentation>All subTitles elements</xs:documentation>
				</xs:annotation>
			</xs:element>
		</xs:sequence>
	</xs:complexType>
	
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
		<xs:attribute name="label" type="xs:string" use="optional">
			<xs:annotation>
				<xs:documentation>Specify label you want to set for the caption asset</xs:documentation>
			</xs:annotation>
		</xs:attribute>
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
	<xs:element name="subTitles" type="T_subTitles" substitutionGroup="item-extension">
		<xs:annotation>
			<xs:documentation>All subTitles elements</xs:documentation>
			<xs:appinfo>
				<example>
					<subTitles>
						<action>update</action>
						<subTitle>...</subTitle>
						<subTitle>...</subTitle>
						<subTitle>...</subTitle>
					</subTitles>
				</example>
			</xs:appinfo>
		</xs:annotation>
	</xs:element>
	<xs:element name="subTitle" type="T_subTitle">
		<xs:annotation>
			<xs:documentation>A single caption asset element</xs:documentation>
			<xs:appinfo>
				<example>
					<subTitle isDefault="true" format="2" lang="Hebrew">
						<tags>
							<tag>tag1</tag>
							<tag>tag2</tag>
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
		
		if(!isset($item->subTitles))
			return;
		
		if(empty($item->subTitles->subTitle))
			return;
		
		KBatchBase::impersonate($this->xmlBulkUploadEngine->getCurrentPartnerId());
		$this->getCurrentCaptionAssets($object->id);
		
		$pluginsErrorResults = array();
		foreach($item->subTitles->subTitle as $caption)
		{
			try {
				$this->handleCaptionAsset($object->id, $object->conversionProfileId, $caption);
			}
			catch (Exception $e)
			{
				KalturaLog::err($this->getContainerName() . ' failed: ' . $e->getMessage());
				$pluginsErrorResults[] = $e->getMessage();
			}
		}
		
		if(count($pluginsErrorResults))
			throw new Exception(implode(', ', $pluginsErrorResults));
		
		KBatchBase::unimpersonate();
	}

	private function handleCaptionAsset($entryId, $conversionProfileId, SimpleXMLElement $caption)
	{
		$captionAssetPlugin = KalturaCaptionClientPlugin::get(KBatchBase::$kClient);
		
		$captionAsset = new KalturaCaptionAsset();
		$captionAsset->tags = $this->xmlBulkUploadEngine->implodeChildElements($caption->tags);

		if(isset($caption->captionAssetId))
			$captionAsset->id = $caption->captionAssetId;
		
		if(isset($caption['captionParamsId']) || isset($caption['captionParams']))
			$captionAsset->captionParamsId = $this->xmlBulkUploadEngine->getAssetParamsId($caption, $conversionProfileId, true, 'caption');
			
		if(isset($caption['isDefault']))
			if(strtolower($caption['isDefault']) == 'true'){
				$captionAsset->isDefault = KalturaNullableBoolean::TRUE_VALUE;
			}else{
				$captionAsset->isDefault = KalturaNullableBoolean::FALSE_VALUE;
			}
		
		if(isset($caption['label']))
			$captionAsset->label = $caption['label'];

		if(isset($caption['format']))
			$captionAsset->format = $caption['format'];
		
		if(isset($caption['lang']))
			$captionAsset->language = $caption['lang'];
			
		$captionAssetId = null;
		if(isset($caption['captionAssetId']))
		{
			$captionAssetId = $caption['captionAssetId'];
		}
		elseif(isset($captionAsset->captionParamsId))
		{
			if(isset($this->currentCaptionAssets[$captionAsset->captionParamsId]))
				$captionAssetId = $this->currentCaptionAssets[$captionAsset->captionParamsId];
		}
		
		if($captionAssetId)
		{
			$captionAssetPlugin->captionAsset->update($captionAssetId, $captionAsset);
		}else
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
		if(!$item->subTitles)
			return;
			
		if(empty($item->subTitles))
			return;
		
		$action = KBulkUploadEngine::$actionsMap[KalturaBulkUploadAction::UPDATE];
		if(isset($item->subTitles->action))
			$action = strtolower($item->subTitles->action);
			
		switch ($action)
		{
			case KBulkUploadEngine::$actionsMap[KalturaBulkUploadAction::UPDATE]:
				$this->handleItemAdded($object, $item);
				break;
			default:
				throw new KalturaBatchException("subTitles->action: $action is not supported", KalturaBatchJobAppErrors::BULK_ACTION_NOT_SUPPORTED);
		}
	}

	private function getCurrentCaptionAssets($entryId)
	{
		$filter = new KalturaCaptionAssetFilter();
		$filter->entryIdEqual = $entryId;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		$captionAssetPlugin = KalturaCaptionClientPlugin::get(KBatchBase::$kClient);
		$captions = $captionAssetPlugin->captionAsset->listAction($filter, $pager);
		
		$this->currentCaptionAssets = array();
		
		if (!isset($captions->objects))
			return;

		foreach ($captions->objects as $caption)
		{
			if($caption->captionParamsId != 0) //there could be multiple captions with captionParamsId=0
				$this->currentCaptionAssets[$caption->captionParamsId] = $caption->id;
		}
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
	
	/* (non-PHPdoc)
	 * @see IKalturaConfigurator::getContainerName()
	*/
	public function getContainerName()
	{
		return 'subTitles';
	}
}
