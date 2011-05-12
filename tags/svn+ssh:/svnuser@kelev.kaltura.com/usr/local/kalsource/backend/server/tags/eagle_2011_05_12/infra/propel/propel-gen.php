<?php
$origWD = getcwd();

$rootFolder = realpath(dirname(__FILE__)."/../..");

// Core
$alphaConfigFolder = "$rootFolder/alpha/config";
chdir($alphaConfigFolder);
passthru("propel-gen $alphaConfigFolder");

// Plugin
$pluginsFolder = "$rootFolder/plugins"; 
foreach(scandir($pluginsFolder) as $pluginDir)
{
	if ($pluginDir[0] == ".")
	{
		continue;
	}
	$path = "$pluginsFolder/$pluginDir";
	if (!is_dir($path))
	{
		continue;
	}
	
	$pluginConfig = "$path/config";
	$buildProps = "$pluginConfig/build.properties";
	if (!file_exists($buildProps))
	{
		continue;
	}

	chdir($pluginConfig);	
	passthru("propel-gen $pluginConfig");
}

chdir($origWD);	
