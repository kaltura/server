<?php
class Kaltura_PartnerPackageHelper
{
	private static $packages = null;
	
	public static function getPackageNameById($id)
	{
		self::loadPackges();
		foreach(self::$packages as $package)
		{
			if ($package->id == $id)
			{
				return $package->name;
			}
		}
		return "N/A"; 
	}
	
	private static function loadPackges()
	{
		if (is_null(self::$packages))
		{
			$client = Kaltura_ClientHelper::getClient();
			self::$packages = $client->systemPartner->getPackages();
		}
	}
}