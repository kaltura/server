<?php
require_once("../../bootstrap.php");
KalturaLog::setContext("TESTME");

$type = null;
if(isset($_GET["type"]))
{
	$type = $_GET["type"];
}
elseif(isset($argv[1]))
{
	$type = $argv[1];
	KalturaLog::setLogger(new KalturaStdoutLogger());
}
else 
{
	exit;
}

$bench_start = microtime(true);
KalturaLog::info( ">------- api_v3 testme type [$type]-------");

function toArrayRecursive(KalturaPropertyInfo $propInfo)
{
	return $propInfo->toArray(true);
}

$subClasses = array();
try
{
	KalturaTypeReflector::setClassInheritMapPath(KAutoloader::buildPath(kConf::get("cache_root_path"), "api_v3", "KalturaClassInheritMap.cache"));
	if(!KalturaTypeReflector::hasClassInheritMapCache())
	{
		$config = new Zend_Config_Ini("../../config/testme.ini");
		$indexConfig = $config->get('testme');
		
		$include = $indexConfig->get("include");
		$exclude = $indexConfig->get("exclude");
		$excludePaths = $indexConfig->get("excludepaths");
		$additional = $indexConfig->get("additional");
		
		$clientGenerator = new DummyForDocsClientGenerator();
		$clientGenerator->setIncludeOrExcludeList($include, $exclude, $excludePaths);
		$clientGenerator->setAdditionalList($additional);
		$clientGenerator->load();
		
		$objects = $clientGenerator->getTypes();
		
		KalturaTypeReflector::setClassMap(array_keys($objects));
	}

	$subClassesNames = KalturaTypeReflector::getSubClasses($type);
	KalturaLog::info( "subClassesNames [" . print_r($subClassesNames, true) . "]");

	foreach($subClassesNames as $subClassName)
	{
		$subClass = new KalturaPropertyInfo($subClassName);
		$subClasses[] = $subClass->toArray();
	}
}
catch ( Exception $ex )
{
	KalturaLog::err ( "<------- api_v3 testme [$type]\n" . 
		 $ex->__toString() .  " " ." -------");
}
//echo "<pre>";
//echo print_r($actionInfo);
echo json_encode($subClasses);
$bench_end = microtime(true);
KalturaLog::info ( "<------- api_v3 testme type [$type][" . ($bench_end - $bench_start) . "] -------");

