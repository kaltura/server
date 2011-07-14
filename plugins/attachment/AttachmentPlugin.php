<?php
/**
 * Enable attachment assets management for entry objects
 * @package plugins.attachment
 */
class AttachmentPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaEnumerator, IKalturaObjectLoader, IKalturaAdminConsoleEntryInvestigate, IKalturaConfigurator, IKalturaSchemaContributor, IKalturaMrssContributor
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
			return array('AttachmentAssetType');
	
		if($baseEnumName == 'assetType')
			return array('AttachmentAssetType');
			
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
	 * @see IKalturaAdminConsoleEntryInvestigate::getEntryInvestigatePlugins()
	 */
	public static function getEntryInvestigatePlugins()
	{
		return array(
			new Kaltura_View_Helper_EntryInvestigateAttachmentAssets(),
		);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaConfigurator::getConfig()
	 */
	public static function getConfig($configName)
	{
		if($configName == 'generator')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/generator.ini');
			
		return null;
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
			
	<xs:complexType name="T_subTitle">
		<xs:sequence>
			<xs:element name="tags" minOccurs="1" maxOccurs="1" type="T_tags" />
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
	
	/* (non-PHPdoc)
	 * @see IKalturaMrssContributor::contributeToSchema()
	 */
	public function contribute(entry $entry, SimpleXMLElement $mrss)
	{
		$types = KalturaPluginManager::getExtendedTypes(assetPeer::OM_CLASS, AttachmentPlugin::getAssetTypeCoreValue(AttachmentAssetType::ATTACHMENT));
		$attachmentAssets = assetPeer::retrieveByEntryId($entry->getId(), $types);
		
		foreach($attachmentAssets as $attachmentAsset)
			$this->contributeAttachmentAssets($attachmentAssets, $mrss);
	}

	/**
	 * @param AttachmentAsset $attachmentAsset
	 * @param SimpleXMLElement $mrss
	 * @return SimpleXMLElement
	 */
	public function contributeAttachmentAssets(AttachmentAsset $attachmentAsset, SimpleXMLElement $mrss)
	{
		$subTitle = $mrss->addChild('subTitle');
		$subTitle->addAttribute('href', kMrssManager::getAssetUrl($attachmentAsset));
		$subTitle->addAttribute('attachmentAssetId', $attachmentAsset->getId());
		$subTitle->addChild('filename', $attachmentAsset->getFilename());
		$subTitle->addChild('title', $attachmentAsset->getTitle());
		$subTitle->addChild('description', $attachmentAsset->getDescription());
		
		$subTitle->addAttribute('format', $attachmentAsset->getContainerFormat());
			
		$tags = $subTitle->addChild('tags');
		foreach(explode(',', $attachmentAsset->getTags()) as $tag)
			$tags->addChild('tag', self::stringToSafeXml($tag));
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
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}
