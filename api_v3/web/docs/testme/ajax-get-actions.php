<?php
require_once("../../../bootstrap.php");
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
    $actionLabel = $actionInfo->action;
    if ($actionInfo->deprecated)
    	$actionLabel .= ' (deprecated)';
    
   	$actions[] = array(
   		'action' => $actionInfo->action, 
   		'name' => $actionName, 
   		'label' => $actionLabel,
   	);
}

echo json_encode($actions);
