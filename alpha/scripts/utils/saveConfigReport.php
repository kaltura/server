<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

$configPath = realpath(__DIR__ . '/../../configurations');
$xmlFilename = "$configPath/history/config." . date('Y.m.d') . '.xml';
$reportFilename = "$configPath/history/report." . date('Y.m.d') . '.log';

$tamplates = array();
$configDir = dir($configPath);
while (false !== ($iniFile = $configDir->read())) 
{
	$matches = null;
	if(preg_match('/^([a-z0-9_]+)\.template\.ini$/', $iniFile, $matches))
	{
		$mapName = $matches[1];
		
		KalturaLog::info("Loads template file [$iniFile]");
		$tamplateConfig = new Zend_Config_Ini("$configPath/$iniFile");
		$tamplates[$mapName] = $tamplateConfig->toArray();
	}
	elseif(preg_match('/^([a-z0-9_]+)\.ini$/', $iniFile, $matches))
	{
		$mapName = $matches[1];
		if($mapName == 'base')
			continue;
		
		KalturaLog::info("Loads ini file [$iniFile] for map [$mapName]");
		kConf::getMap($mapName);
	}
}
$configDir->close();

//try
//{
//	$config = new Zend_Config(kConf::getAll());
//	$configWriter = new Zend_Config_Writer_Xml();
//	$configWriter->write($xmlFilename, $config);
//	KalturaLog::info("Saved config to history [$xmlFilename]");
//}
//catch(Exception $e)
//{
//	KalturaLog::err($e->getMessage());
//}

$reports = array();
foreach($tamplates as $mapName => $tamplate)
{
	try
	{
		$map = kConf::getMap($mapName);
	}
	catch(Exception $e)
	{
		$msg = $e->getMessage();
		$reports[] = $msg;
		KalturaLog::debug($msg);
		continue;
	}
	$iniFile = realpath("$configPath/$mapName.ini");
	
	$mapReports = compareMaps($mapName, $tamplate, $map);
	
	if(count($mapReports))
	{
		$reports[] = "Config map [$mapName] file [$iniFile] issues:";
		foreach($mapReports as $report)
			$reports[] = " - $report";
		$reports[] = '';
	}
	else
	{
		$msg = "Valid config map [$mapName] file [$iniFile]";
		$reports[] = $msg;
		KalturaLog::debug($msg);
	}
}

file_put_contents($reportFilename, implode("\n", $reports));
KalturaLog::info("Saved report to history [$reportFilename]");


function compareMaps($mapName, $tamplate, $map)
{
	$reports = array();
	
	if(!is_array($map))
	{
		return array("Map is not array [$mapName]");
	}
	
	$tamplateKeys = array_keys($tamplate);
	$mapKeys = array_keys($map);
	
	$diff = array_diff($tamplateKeys, $mapKeys);
	if(count($diff))
	{
		$foundIssues = true;
	
		foreach($diff as $key)
		{
			$reports[] = "Missing key [$mapName.$key]";
		}
	}
	
	$keys = array_intersect($tamplateKeys, $mapKeys);
	foreach($keys as $key)
	{
		if(!is_array($tamplate[$key]))
			continue;
		
		$mapReports = compareMaps("$mapName.$key", $tamplate[$key], $map[$key]);
		
		if(!count($mapReports))
			continue;
		
		foreach($mapReports as $report)
			$reports[] = $report;
	}
	
	return $reports;
}
