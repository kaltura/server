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

}