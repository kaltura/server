<?php
require_once("../../bootstrap.php");
KalturaLog::setContext("TESTME");

$service = $_GET["service"];
$serviceMap = KalturaServicesMap::getMap();
$serviceActionItem = $serviceMap[strtolower($service)];
if(!$service)
	exit;


/* @var $serviceActionItem KalturaServiceActionItem */
$actionsArray = $serviceActionItem->actionMap;
$actionNames = array_keys($actionsArray);
sort($actionNames);

$actions = array();
foreach($actionNames as $actionName)
{
    $actionReflector = new KalturaActionReflector($service, $actionName, $actionsArray[$actionName]);
    $actionInfo = $actionReflector->getActionInfo();
    $actionDisplayName = $actionReflector->getActionId();
    $actionLabel = $actionReflector->getActionName();
    if ($actionInfo->deprecated)
    	$actionLabel .= ' (deprecated)';
    
   	$actions[] = array(
   		'action' => $actionName,
   		'name' => $actionName, 
   		'label' => $actionLabel,
   	);
}

echo json_encode($actions);
