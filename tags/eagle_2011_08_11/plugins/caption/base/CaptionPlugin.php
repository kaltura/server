<?php
/**
 * Enable caption assets management for entry objects
 * @package plugins.caption
 */
class CaptionPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaEnumerator, IKalturaObjectLoader, IKalturaAdminConsoleEntryInvestigate, IKalturaConfigurator, IKalturaSchemaContributor, IKalturaMrssContributor
{
	const PLUGIN_NAME = 'caption';
	
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
			'captionAsset' => 'CaptionAssetService',
			'captionParams' => 'CaptionParamsService',
		);
		return $map;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('CaptionAssetType');
	
		if($baseEnumName == 'assetType')
			return array('CaptionAssetType');
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KalturaAsset' && $enumValue == self::getAssetTypeCoreValue(CaptionAssetType::CAPTION))
			return new KalturaCaptionAsset();
	
		if($baseClass == 'KalturaAssetParams' && $enumValue == self::getAssetTypeCoreValue(CaptionAssetType::CAPTION))
			return new KalturaCaptionParams();
	
		return null;
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'asset' && $enumValue == self::getAssetTypeCoreValue(CaptionAssetType::CAPTION))
			return 'CaptionAsset';
	
		if($baseClass == 'assetParams' && $enumValue == self::getAssetTypeCoreValue(CaptionAssetType::CAPTION))
			return 'CaptionParams';
			
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaAdminConsoleEntryInvestigate::getEntryInvestigatePlugins()
	 */
	public static function getEntryInvestigatePlugins()
	{
		return array(
			new Kaltura_View_Helper_EntryInvestigateCaptionAssets(),
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
			<xs:element ref="subtitle-extension" minOccurs="0" maxOccurs="unbounded" />
		</xs:sequence>
		
		<xs:attribute name="captionParamsId" type="xs:int" use="optional" />
		<xs:attribute name="captionParams" type="xs:string" use="optional" />
		<xs:attribute name="captionAssetId" type="xs:string" use="optional" />
		<xs:attribute name="isDefault" type="xs:boolean" use="optional" />
		<xs:attribute name="format" type="KalturaCaptionType" use="optional" />
		<xs:attribute name="lang" type="KalturaLanguage" use="optional" />
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
	public function contribute(BaseObject $object, SimpleXMLElement $mrss, kMrssParameters $mrssParams = null)
	{
		if(!($object instanceof entry))
			return;
			
		$types = KalturaPluginManager::getExtendedTypes(assetPeer::OM_CLASS, CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION));
		$captionAssets = assetPeer::retrieveByEntryId($object->getId(), $types);
		
		foreach($captionAssets as $captionAsset)
			$this->contributeCaptionAssets($captionAsset, $mrss);
	}

	/**
	 * @param CaptionAsset $captionAsset
	 * @param SimpleXMLElement $mrss
	 * @return SimpleXMLElement
	 */
	public function contributeCaptionAssets(CaptionAsset $captionAsset, SimpleXMLElement $mrss)
	{
		$subTitle = $mrss->addChild('subTitle');
		$subTitle->addAttribute('href', $captionAsset->getDownloadUrl(true));
		$subTitle->addAttribute('captionAssetId', $captionAsset->getId());
		$subTitle->addAttribute('isDefault', ($captionAsset->getDefault() ? 'true' : 'false'));
		$subTitle->addAttribute('format', $captionAsset->getContainerFormat());
		$subTitle->addAttribute('lang', $captionAsset->getLanguage());
		if($captionAsset->getFlavorParamsId())
			$subTitle->addAttribute('captionParamsId', $captionAsset->getFlavorParamsId());
			
		$tags = $subTitle->addChild('tags');
		foreach(explode(',', $captionAsset->getTags()) as $tag)
			$tags->addChild('tag', kMrssManager::stringToSafeXml($tag));
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
