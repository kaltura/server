<?php
/**
 * Enable attachment assets management for entry objects
 * @package plugins.attachment
 */
class AttachmentPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaEnumerator, IKalturaObjectLoader, IKalturaApplicationPartialView, IKalturaSchemaContributor, IKalturaMrssContributor
{
	const PLUGIN_NAME = 'attachment';
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		return $partner->getPluginEnabled(self::PLUGIN_NAME);
	}

	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap()
	{
		$map = array(
			'attachmentAsset' => 'AttachmentAssetService',
		);
		return $map;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('AttachmentAssetType', 'AttachmentObjectFeatureType');
	
		if($baseEnumName == 'assetType')
			return array('AttachmentAssetType');
			
		if ($baseEnumName == 'ObjectFeatureType')
			return array ('AttachmentObjectFeatureType');
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KalturaAsset' && $enumValue == self::getAssetTypeCoreValue(AttachmentAssetType::ATTACHMENT))
			return new KalturaAttachmentAsset();
	
		return null;
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'asset' && $enumValue == self::getAssetTypeCoreValue(AttachmentAssetType::ATTACHMENT))
			return 'AttachmentAsset';
	
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaApplicationPartialView::getApplicationPartialViews()
	 */
	public static function getApplicationPartialViews($controller, $action)
	{
		if($controller == 'batch' && $action == 'entryInvestigation')
		{
			return array(
				new Kaltura_View_Helper_EntryInvestigateAttachmentAssets(),
			);
		}
		
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function contributeToSchema($type)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		if($coreType != SchemaType::SYNDICATION)
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
				<xs:documentation>The asset unique id</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="format" type="KalturaAttachmentType" use="optional">
			<xs:annotation>
				<xs:documentation>Attachment asset file format</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="url" type="xs:string" use="optional">
			<xs:annotation>
				<xs:documentation>Attachment asset file download URL</xs:documentation>
			</xs:annotation>
		</xs:attribute>
						
	</xs:complexType>
	
	<xs:element name="attachment-extension" />
	<xs:element name="attachment" type="T_attachment" substitutionGroup="item-extension">
		<xs:annotation>
			<xs:documentation>Attachment asset element</xs:documentation>
			<xs:appinfo>
				<example>
					<attachment url="http://kaltura.domain/path/to/attachment/asset/file.txt" attachmentAssetId="{attachment asset id}" format="1">
						<tags>
							<tag>example</tag>
							<tag>my_tag</tag>
						</tags>
						<filename>my_file_name.txt</filename>
						<title>my attachment asset title</title>
						<description>my attachment asset free text description</description>
					</attachment>
				</example>
			</xs:appinfo>
		</xs:annotation>
	</xs:element>
		';
		
		return $xsd;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaMrssContributor::contribute()
	 */
	public function contribute(BaseObject $object, SimpleXMLElement $mrss, kMrssParameters $mrssParams = null)
	{
		if(!($object instanceof entry))
			return;
			
		$types = KalturaPluginManager::getExtendedTypes(assetPeer::OM_CLASS, AttachmentPlugin::getAssetTypeCoreValue(AttachmentAssetType::ATTACHMENT));
		$attachmentAssets = assetPeer::retrieveByEntryId($object->getId(), $types);
		
		foreach($attachmentAssets as $attachmentAsset)
			$this->contributeAttachmentAssets($attachmentAsset, $mrss);
	}

	/**
	 * @param AttachmentAsset $attachmentAsset
	 * @param SimpleXMLElement $mrss
	 * @return SimpleXMLElement
	 */
	public function contributeAttachmentAssets(AttachmentAsset $attachmentAsset, SimpleXMLElement $mrss)
	{
		$attachment = $mrss->addChild('attachment');
		$attachment->addAttribute('url', $attachmentAsset->getDownloadUrl(true));
		$attachment->addAttribute('attachmentAssetId', $attachmentAsset->getId());
		$attachment->addAttribute('format', $attachmentAsset->getContainerFormat());
		
		$tags = $attachment->addChild('tags');
		foreach(explode(',', $attachmentAsset->getTags()) as $tag)
			$tags->addChild('tag', kMrssManager::stringToSafeXml($tag));
			
		$attachment->addChild('filename', $attachmentAsset->getFilename());
		$attachment->addChild('title', $attachmentAsset->getTitle());
		$attachment->addChild('description', $attachmentAsset->getPartnerDescription());
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getAssetTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('assetType', $value);
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getObjectFeatureTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('ObjectFeatureType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaMrssContributor::getObjectFeatureType()
	 */
	public function getObjectFeatureType ()
	{
		return self::getObjectFeatureTypeCoreValue(AttachmentObjectFeatureType::ATTACHMENT);
	}
}
