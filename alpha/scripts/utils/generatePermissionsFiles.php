<?php

$dir = __DIR__ . '/permissions';
chdir(__DIR__ . '/../');
require_once(__DIR__ . '/../bootstrap.php');

if(!file_exists($dir))
	mkdir($dir, 0750);

$criteria = new Criteria();
$criteria->add(PermissionPeer::PARTNER_ID, array(0, -1, -2, -3), Criteria::IN);
$criteria->add(PermissionPeer::STATUS, PermissionStatus::ACTIVE);
$criteria->addAscendingOrderByColumn(PermissionPeer::NAME);
$permissions = PermissionPeer::doSelect($criteria);
KalturaLog::debug("Found [" . count($permissions) . "] permissions");

$files = array();
$permissionArray = array();
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
	
	$permissionArray[$permission->getId()] = $permission;
	
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
			
		$fileName = "service.$service.ini";
		$fileName = str_replace('_', '.', $fileName);			
		$file = fopen("$dir/$fileName", 'w');
		fputs($file, "[action_permission_items]\n");
		$currentIndex = 0;
		$currentService = $service;
	}
	$currentIndex++;
	
	fputs($file, "permissionItem{$currentIndex}.service = $service\n");
	fputs($file, "permissionItem{$currentIndex}.action = $action\n");
	fputs($file, "permissionItem{$currentIndex}.partnerId = $partnerId\n");
	fputs($file, "permissionItem{$currentIndex}.param3 = $param3\n");
	fputs($file, "permissionItem{$currentIndex}.param4 = $param4\n");
	fputs($file, "permissionItem{$currentIndex}.param5 = $param5\n");
	fputs($file, "permissionItem{$currentIndex}.tags = $tags\n");
	
	$criteria = new Criteria();
	$criteria->add(PermissionToPermissionItemPeer::PERMISSION_ITEM_ID, $actionPermissionItem->getId());
	$permissionToPermissionItems = PermissionToPermissionItemPeer::doSelect($criteria);
	$permissions = array();
	foreach($permissionToPermissionItems as $permissionToPermissionItem)
	{
		/* @var $permissionToPermissionItem PermissionToPermissionItem */
		if(!isset($permissionArray[$permissionToPermissionItem->getPermissionId()]))
			continue;
			
		$permission = $permissionArray[$permissionToPermissionItem->getPermissionId()];
		/* @var $permission Permission */
		
		$permissionName = $permission->getName();
		$permissionPartnerId = $permission->getPartnerId();
		if($permissionPartnerId != $partnerId)
			$permissionName = "{$permissionPartnerId}>{$permissionName}";
			
		$permissions[] = $permissionName;
	}
	$permissions = implode(', ', $permissions);
	fputs($file, "permissionItem{$currentIndex}.permissions = $permissions\n");
	
	fputs($file, "\n");
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
		fputs($file, "[parameter_permission_items]\n");
		$currentIndex = 0;
		$currentObject = $object;
	}
	$currentIndex++;
	
	fputs($file, "permissionItem{$currentIndex}.object = $object\n");
	fputs($file, "permissionItem{$currentIndex}.parameter = $parameter\n");
	fputs($file, "permissionItem{$currentIndex}.action = $action\n");
	fputs($file, "permissionItem{$currentIndex}.partnerId = $partnerId\n");
	fputs($file, "permissionItem{$currentIndex}.param4 = $param4\n");
	fputs($file, "permissionItem{$currentIndex}.param5 = $param5\n");
	fputs($file, "permissionItem{$currentIndex}.tags = $tags\n");
	
	$criteria = new Criteria();
	$criteria->add(PermissionToPermissionItemPeer::PERMISSION_ITEM_ID, $parameterPermissionItem->getId());
	$permissionToPermissionItems = PermissionToPermissionItemPeer::doSelect($criteria);
	$permissions = array();
	foreach($permissionToPermissionItems as $permissionToPermissionItem)
	{
		/* @var $permissionToPermissionItem PermissionToPermissionItem */
		if(!isset($permissionArray[$permissionToPermissionItem->getPermissionId()]))
			continue;
			
		$permission = $permissionArray[$permissionToPermissionItem->getPermissionId()];
		/* @var $permission Permission */
		
		$permissionName = $permission->getName();
		$permissionPartnerId = $permission->getPartnerId();
		if($permissionPartnerId != $partnerId)
			$permissionName = "{$permissionPartnerId}>{$permissionName}";
			
		$permissions[] = $permissionName;
	}
	$permissions = implode(', ', $permissions);
	fputs($file, "permissionItem{$currentIndex}.permissions = $permissions\n");
	
	fputs($file, "\n");
}
if($file)
	fclose($file);

echo "Done.";
