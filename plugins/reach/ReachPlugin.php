<?php
/**
 * Enable time based cue point objects management on entry objects
 * @package plugins.reach
 */
class ReachPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaVersion,IKalturaAdminConsolePages
{
	const PLUGIN_NAME = 'reach';
	const PLUGIN_VERSION_MAJOR = 1;
	const PLUGIN_VERSION_MINOR = 0;
	const PLUGIN_VERSION_BUILD = 0;
	const REACH_MANAGER = 'kReachManager';

	/*
	 * (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'BaseVendorCatalogItem' && $enumValue == KalturaVendorCatalogItemType::CAPTIONS)
			return 'KalturaVendorCaptionsCatalogItem';

		if($baseClass == 'BaseVendorCatalogItem' && $enumValue == KalturaVendorCatalogItemType::TRANSLATION)
			return 'KalturaVendorTranslationCatalogItem';

		return null;
	}

	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaVersion::getVersion()
	 */
	public static function getVersion()
	{
		return new KalturaVersion(
			self::PLUGIN_VERSION_MAJOR,
			self::PLUGIN_VERSION_MINOR,
			self::PLUGIN_VERSION_BUILD
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
		if(in_array($partnerId, array(Partner::ADMIN_CONSOLE_PARTNER_ID, Partner::BATCH_PARTNER_ID, PartnerPeer::GLOBAL_PARTNER)))
			return true;
		
		$partner = PartnerPeer::retrieveByPK($partnerId);
		return $partner->getPluginEnabled(self::PLUGIN_NAME);
	}


	public static function isAllowAdminApi($actionApi = null)
	{
		$currentPermissions = Infra_AclHelper::getCurrentPermissions();
		return ($currentPermissions && in_array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_CATALOG_ITEM_MODIFY, $currentPermissions));
	}

	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap()
	{
		$map = array(
			'vendorCatalogItem' => 'VendorCatalogItemService',
		);
		return $map;
	}

 	/*
 	 * @see IKalturaAdminConsolePages::getApplicationPages()
 	 */
	public static function getApplicationPages()
	{
 		$pages = array();
		$pages[] = new CatalogItemListAction();
		$pages[] = new CatalogItemConfigureAction();
		$pages[] = new CatalogItemSetStatusAction();
		return $pages;
	}
	
	//TODO add reach plugin permission
}
