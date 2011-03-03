<?php

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../../'));
define('SPHINX_CONFIG_DIR', ROOT_DIR . '/configurations/sphinx/');

require_once(ROOT_DIR . '/alpha/config/kConf.php');
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/deploy/classMap.cache');
KAutoloader::register();

error_reporting(E_ALL);
KalturaLog::setLogger(new KalturaStdoutLogger());

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();


$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaEnumerator');
foreach($pluginInstances as $pluginInstance)
{
	$pluginName = $pluginInstance->getPluginName();
	KalturaLog::debug("Installs plugin [$pluginName]");
	$enums = $pluginInstance->getEnums();
	foreach($enums as $enum)
	{
		$interfaces = class_implements($enum);
		foreach($interfaces as $interface)
		{
			if($interface == 'IKalturaPluginEnum' || $interface == 'BaseEnum')
				continue;
		
			$interfaceInterfaces = class_implements($interface);
			if(!in_array('BaseEnum', $interfaceInterfaces))
				continue;
				
			KalturaLog::debug("Installs enum [$enum] of type [$interface]");
			$values = call_user_func(array($enum, 'getAdditionalValues'));
			foreach($values as $value)
			{
				$enumValue = $pluginName . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $value;
				KalturaLog::debug("Installs enum value [$enumValue] to type [$interface]");
				kPluginableEnumsManager::apiToCore($interface, $enumValue);
			}
		}
	}
}

