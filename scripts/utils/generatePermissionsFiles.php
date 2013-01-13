<?php

$dir = __DIR__ . '/permissions';
chdir(__DIR__ . '/../');
require_once 'bootstrap.php';

if(!file_exists($dir))
	mkdir($dir, 0750);

$criteria = new Criteria();
$criteria->add(PermissionPeer::PARTNER_ID, array(0, -1, -2, -3), Criteria::IN);
$criteria->add(PermissionPeer::STATUS, PermissionStatus::ACTIVE);
$criteria->addAscendingOrderByColumn(PermissionPeer::NAME);
$permissions = PermissionPeer::doSelect($criteria);
KalturaLog::debug("Found [" . count($permissions) . "] permissions");

$files = array();
$permissionNames = array();
foreach($permissions as $index => $permission)
{
	/* @var $permission Permission */
	
	$partnerId = $permission->getPartnerId();
	$type = $permission->getType();
	$name = $permission->getName();
	$friendlyName = $permission->getFriendlyName();
	$description = $permission->getDescription();
	$dependsOnPermissionNames = $permission->getDependsOnPermissionNames();
	$tags = $permission->getTags();
	$partnerGroup = $permission->getPartnerGroup();
	
	$permissionNames[$permission->getId()] = $name;
	
	if(!isset($files[$partnerId]))
	{
		$files[$partnerId] = fopen("$dir/partner.$partnerId.ini", 'w');
		fputs($files[$partnerId], "[permissions]\n");
	}
	
	fputs($files[$partnerId], "permission{$index}.partnerId = $partnerId\n");
	fputs($files[$partnerId], "permission{$index}.type = $type\n");
	fputs($files[$partnerId], "permission{$index}.name = $name\n");
	fputs($files[$partnerId], "permission{$index}.friendlyName = \"$friendlyName\"\n");
	fputs($files[$partnerId], "permission{$index}.description = \"$description\"\n");
	fputs($files[$partnerId], "permission{$index}.dependsOnPermissionNames = $dependsOnPermissionNames\n");
	fputs($files[$partnerId], "permission{$index}.tags = $tags\n");
	fputs($files[$partnerId], "permission{$index}.partnerGroup = $partnerGroup\n");
	fputs($files[$partnerId], "\n");
}
foreach($files as $file)
	fclose($file);

kMemoryManager::clearMemory();


$criteria = new Criteria();
$criteria->add(PermissionItemPeer::PARTNER_ID, array(0, -1, -2, -3), Criteria::IN);
$criteria->add(PermissionItemPeer::TYPE, PermissionItemType::API_ACTION_ITEM);
$criteria->addAscendingOrderByColumn(PermissionItemPeer::PARAM_1);
$criteria->addAscendingOrderByColumn(PermissionItemPeer::PARAM_2);
$permissionItems = PermissionItemPeer::doSelect($criteria);
KalturaLog::debug("Found [" . count($permissionItems) . "] action permission items");

$file = null;
$currentIndex = null;
$currentService = null;
foreach($permissionItems as $actionPermissionItem)
{
	/* @var $actionPermissionItem kApiActionPermissionItem */
	
	$service = $actionPermissionItem->getService();
	$action = $actionPermissionItem->getAction();
	$partnerId = $actionPermissionItem->getPartnerId();
	$param3 = $actionPermissionItem->getParam3();
	$param4 = $actionPermissionItem->getParam4();
	$param5 = $actionPermissionItem->getParam5();
	$tags = $actionPermissionItem->getTags();
	
	if($service != $currentService)
	{
		if($file)
			fclose($file);
			
		$file = fopen("$dir/service.$service.ini", 'w');
		fputs($files[$partnerId], "[parameter_permission_items]\n");
		$currentIndex = 0;
		$currentService = $service;
	}
	$currentIndex++;
	
	fputs($files[$partnerId], "permissionItem{$index}.service = $service\n");
	fputs($files[$partnerId], "permissionItem{$index}.action = $action\n");
	fputs($files[$partnerId], "permissionItem{$index}.partnerId = $partnerId\n");
	fputs($files[$partnerId], "permissionItem{$index}.param3 = $param3\n");
	fputs($files[$partnerId], "permissionItem{$index}.param4 = $param4\n");
	fputs($files[$partnerId], "permissionItem{$index}.param5 = $param5\n");
	fputs($files[$partnerId], "permissionItem{$index}.tags = $tags\n");
	fputs($files[$partnerId], "\n");
}
if($file)
	fclose($file);

kMemoryManager::clearMemory();


$criteria = new Criteria();
$criteria->add(PermissionItemPeer::PARTNER_ID, array(0, -1, -2, -3), Criteria::IN);
$criteria->add(PermissionItemPeer::TYPE, PermissionItemType::API_PARAMETER_ITEM);
$criteria->addAscendingOrderByColumn(PermissionItemPeer::PARAM_1);
$criteria->addAscendingOrderByColumn(PermissionItemPeer::PARAM_2);
$permissionItems = PermissionItemPeer::doSelect($criteria);
KalturaLog::debug("Found [" . count($permissionItems) . "] parameter permission items");

$file = null;
$currentIndex = null;
$currentObject = null;
foreach($permissionItems as $parameterPermissionItem)
{
	/* @var $parameterPermissionItem kApiParameterPermissionItem */
	
	$object = $parameterPermissionItem->getObject();
	$parameter = $parameterPermissionItem->getParameter();
	$action = $parameterPermissionItem->getAction();
	$partnerId = $parameterPermissionItem->getPartnerId();
	$param4 = $parameterPermissionItem->getParam4();
	$param5 = $parameterPermissionItem->getParam5();
	$tags = $parameterPermissionItem->getTags();
	
	if($object != $currentObject)
	{
		if($file)
			fclose($file);
			
		$file = fopen("$dir/object.$object.ini", 'w');
		fputs($files[$partnerId], "[parameter_permission_items]\n");
		$currentIndex = 0;
		$currentObject = $object;
	}
	$currentIndex++;
	
	fputs($files[$partnerId], "permissionItem{$index}.object = $object\n");
	fputs($files[$partnerId], "permissionItem{$index}.parameter = $parameter\n");
	fputs($files[$partnerId], "permissionItem{$index}.action = $action\n");
	fputs($files[$partnerId], "permissionItem{$index}.partnerId = $partnerId\n");
	fputs($files[$partnerId], "permissionItem{$index}.param4 = $param4\n");
	fputs($files[$partnerId], "permissionItem{$index}.param5 = $param5\n");
	fputs($files[$partnerId], "permissionItem{$index}.tags = $tags\n");
	fputs($files[$partnerId], "\n");
}
if($file)
	fclose($file);

echo "Done.";
