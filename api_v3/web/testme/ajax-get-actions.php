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
    $actionName = $actionInfo->action;
    if ($actionInfo->deprecated)
    	$actionName .= ' (deprecated)';
   	$actions[] = array($actionInfo->action, $actionName);
}

echo json_encode($actions);
?>