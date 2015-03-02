<?php
/**
 * @package server-infra
 * @subpackage propel
 */
function searchFolder($pluginsFolder, $level = 1)
{
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
		
		if ($level < 4)
		{
			searchFolder($path, $level + 1);
		}
		
		$pluginConfig = "$path/config";
		$buildProps = "$pluginConfig/build.properties";
		if (!file_exists($buildProps))
		{
			continue;
		}

		if(!is_dir($pluginConfig))
			throw new Exception("Illegal input was supplied.");
		
		chdir($pluginConfig);	
		print $pluginConfig;
		passthru("propel-gen $pluginConfig");
	}
}

$origWD = getcwd();

$rootFolder = realpath(dirname(__FILE__)."/../../../../..");

// Core
$alphaConfigFolder = "$rootFolder/alpha/config";
if(!is_dir($alphaConfigFolder))
	throw new Exception("Illegal input was supplied.");

chdir($alphaConfigFolder);
passthru("propel-gen $alphaConfigFolder");

// Plugin
$pluginsFolder = "$rootFolder/plugins"; 
searchFolder($pluginsFolder);

chdir($origWD);	
