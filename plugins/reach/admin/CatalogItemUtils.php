<?php

/**
 * @package plugins.reach
 * @subpackage Admin
 */

class CatalogItemUtils
{

	const ADMIN_CONSOLE_PARTNER = "-2";

	public static function getCatalogItemById($catalogItemById)
	{
		$reachPluginClient = self::getPluginByName('Kaltura_Client_Reach_Plugin');
		return $reachPluginClient->vendorCatalogItem->get($catalogItemById);
	}


	public static function getPluginByName($name, $partnerId = null) {
		$client = Infra_ClientHelper::getClient();
		if ($partnerId)
			$client->setPartnerId($partnerId);
		else
			$client->setPartnerId(self::ADMIN_CONSOLE_PARTNER);
		$plugin = $name::get($client);
		return $plugin;
	}

	public static function createNewCatalogItem($formData , $partnerId)
	{
		$catalogItem = self::createCatalogItem($formData);
		$reachPlugin = self::getPluginByName('Kaltura_Client_Reach_Plugin', $partnerId);

		$res = $reachPlugin->vendorCatalogItem->add($catalogItem);
		return $res;
	}

	public static function updateCatalogItem($partnerId, $catalogItemId, $catalogItem)
	{
		$reachPlugin = self::getPluginByName('Kaltura_Client_Reach_Plugin', $partnerId);
		$res = $reachPlugin->vendorCatalogItem->update($catalogItemId, $catalogItem);
		return $res;
	}


	private static function createCatalogItem($formData, $isDefault = false)
	{
		$catalogItem = null;
		if ($formData['catalogItemTypeForView'] == Kaltura_Client_Reach_Enum_VendorCatalogItemType::CAPTIONS)
		{
			$catalogItem = new Kaltura_Client_Reach_Type_VendorCaptionsCatalogItem();
		}
		elseif ($formData['catalogItemTypeForView'] == Kaltura_Client_Reach_Enum_VendorCatalogItemType::TRANSLATION)
		{
			$catalogItem = new Kaltura_Client_Reach_Type_VendorTranslationCatalogItem();
			$catalogItem->targetLanguage = $formData['targetLanguage'];
		}

		$catalogItem->name = $formData['name'];;
		$catalogItem->systemName = $formData['systemName'];
		$catalogItem->sourceLanguage = $formData['sourceLanguage'];
		$catalogItem->outputFormat= $formData['outputFormat'];
		$catalogItem->serviceType = $formData['serviceType'];
		$catalogItem->turnAroundTime = $formData['turnAroundTime'];
		$catalogItem->vendorPartnerId = $formData['partnerId'];
		$catalogItem->enableSpeakerId = $formData['enableSpeakerId'];
		$catalogItem->pricing = new Kaltura_Client_Reach_Type_VendorCatalogItemPricing();
		$catalogItem->pricing->pricePerUnit = 1000;
		$catalogItem->pricing->priceFunction = Kaltura_Client_Reach_Enum_VendorCatalogItemPriceFunction::PRICE_PER_MINUTE;
		$catalogItem->isDefault = $formData['isDefault'];
		$catalogItem->fixedPriceAddons = $formData['fixedPriceAddons'];

		return $catalogItem;
	}

}