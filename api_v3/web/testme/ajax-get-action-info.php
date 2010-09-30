<?php
require_once("../../bootstrap.php");
KalturaLog::setContext("TESTME");
$service = $_GET["service"];
$action = $_GET["action"];
$bench_start = microtime(true);
KalturaLog::INFO ( ">------- api_v3 testme [$service][$action]-------");

function toArrayRecursive(KalturaPropertyInfo $propInfo)
{
	return $propInfo->toArray(true);
}

KalturaTypeReflector::setClassInheritMapPath(KAutoloader::buildPath(KALTURA_API_PATH, "cache", "KalturaClassInheritMap.cache"));
if(!KalturaTypeReflector::hasClassInheritMapCache())
{
	$config = new Zend_Config_Ini("../../config/testme.ini");
	$indexConfig = $config->get('index');
	
	$include = $indexConfig->get("include");
	$exclude = $indexConfig->get("exclude");
	$additional = $indexConfig->get("additional");
	
	$clientGenerator = new DummyForDocsClientGenerator();
	$clientGenerator->setIncludeOrExcludeList($include, $exclude);
	$clientGenerator->setAdditionalList($additional);
	$clientGenerator->load();
	
	$objects = $clientGenerator->getObjects();
	
	KalturaTypeReflector::setClassMap(array_keys($objects));
}


$actionInfo = null;
try
{
	$serviceReflector = new KalturaServiceReflector($service);
	
	$actionParams = $serviceReflector->getActionParams($action);
	$actionInfo = $serviceReflector->getActionInfo($action);
	
	$actionInfo = array(
		"actionParams" => array(),
	    "description" => $actionInfo->description
	);

	foreach($actionParams as $actionParam)
	{
		$actionInfo["actionParams"][] = toArrayRecursive($actionParam); 
	}
}
catch ( Exception $ex )
{
	KalturaLog::ERR ( "<------- api_v3 testme [$service][$action\n" . 
		 $ex->__toString() .  " " ." -------");
}
//echo "<pre>";
//echo print_r($actionInfo);
echo json_encode($actionInfo);
$bench_end = microtime(true);
KalturaLog::INFO ( "<------- api_v3 testme [$service][$action][" . ($bench_end - $bench_start) . "] -------");

?>