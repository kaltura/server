<?php
class corputilsPlugin extends KalturaPlugin
{
	public static function getServicesMap()
	{
		//$extraServicePath = realpath(dirname(__FILE__).'/../services/corputilsExtServices.php');
		//return array('_corputils' => $extraServicePath);
		return array('_corputils' => '_corputilsService');
	}
	
	public static function getServiceConfig()
	{
		return realpath(dirname(__FILE__).'/../config/corputils.ct');
	}

	public static function getDatabaseConfig()
	{
//		$config = new Zend_Config_Ini(dirname(__FILE__).'/../config/database.ini');
//		return $config->toArray();
	}
	
	public static function isAllowedPartner($partnerId)
	{
		if($partnerId == -10)
			return true;
		
		return false;
	}
}
?>
