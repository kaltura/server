<?php
require_once("../../bootstrap.php");
KalturaLog::setContext("TESTME");

$service = $_GET["service"];
$serviceReflector = new KalturaServiceReflector($service);

$actionsArray = $serviceReflector->getActions();
$actionNames = array_keys($actionsArray);
sort($actionNames);

$actions = array();
foreach($actionNames as $actionName)
{
    $actionInfo = $serviceReflector->getActionInfo($actionName);
    if(!$actionInfo->deprecated)
    	$actions[] = $actionInfo->action;
}

echo json_encode($actions);
?>