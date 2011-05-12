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


$sphinxBase = SPHINX_CONFIG_DIR . 'base.conf';
$sphinxConfig = SPHINX_CONFIG_DIR . 'kaltura.conf';

if(!file_exists($sphinxBase))
{
	echo "Sphinx base file [$sphinxBase] not found\n";
	exit;
}

copy($sphinxBase, $sphinxConfig);

$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaSphinxConfiguration');
foreach($pluginInstances as $pluginInstance)
{
	$filePath = $pluginInstance->getSphinxConfigPath();
	if($filePath && file_exists($filePath))
	{
		echo "Appending config file [$filePath]\n";
		$config = file_get_contents($filePath);
		file_put_contents($sphinxConfig, $config, FILE_APPEND);
	}
}

