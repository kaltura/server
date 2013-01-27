<?php
require_once("../../bootstrap.php");
KalturaLog::setContext("TESTME");
$service = $_GET["service"];
$action = $_GET["action"];
$bench_start = microtime(true);
KalturaLog::INFO ( ">------- api_v3 testme [$service][$action]-------");

function toArrayRecursive(KalturaPropertyInfo $propInfo)
{
	return $propInfo->toArray();
}

$serviceMap = KalturaServicesMap::getMap();
$actionInfo = null;

if (!array_key_exists(strtolower($service), $serviceMap))
{
	$msg = "<------- api_v3 testme [$service][$action] not found -------";
	KalturaLog::ERR ($msg);
	die($msg);
}

try
{
	
	$serviceReflector = $serviceMap[strtolower($service)];
	/* @var $serviceReflector KalturaServiceActionItem */
	$actionReflector = new KalturaActionReflector($service, $action, $serviceReflector->actionMap[$action]);
	$actionParams = $actionReflector->getActionParams();
	$actionInfo = $actionReflector->getActionInfo();
	
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
	$msg = "<------- api_v3 testme [$service][$action\n" . $ex->__toString() .  " " ." -------";
	KalturaLog::ERR ($msg);
	die($msg);
}
//echo "<pre>";
//echo print_r($actionInfo);
echo json_encode($actionInfo);
$bench_end = microtime(true);
KalturaLog::INFO ( "<------- api_v3 testme [$service][$action][" . ($bench_end - $bench_start) . "] -------");

?>