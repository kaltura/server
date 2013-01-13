<?php

$dir = __DIR__ . '/permissions';
chdir('/opt/kaltura/app/scripts/');
require_once 'bootstrap.php';

if(!file_exists($dir))
	mkdir($dir, 0750);

$criteria = new Criteria();
$criteria->add(PermissionPeer::PARTNER_ID, array(0, -1, -2, -3), Criteria::IN);
$criteria->add(PermissionPeer::STATUS, PermissionStatus::ACTIVE);
$criteria->addAscendingOrderByColumn(PermissionPeer::NAME);
$permissions = PermissionPeer::doSelect($criteria);

$files = array();
KalturaLog::debug("Found [" . count($permissions) . "] permissions");
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

$file = null;
$currentIndex = null;
$currentService = null;
foreach($permissionItems as $permissionItem)
{
	/* @var $permissionItem kApiActionPermissionItem */
	
	$service = $permissionItem->getService();
	$action = $permissionItem->getAction();
	
	if($service != $currentService)
	{
		if($file)
			fclose($file);
			
		$file = fopen("$dir/$service.ini", 'w');
		$currentIndex = 0;
		$currentService = $service;
	}
	$currentIndex++;
	
	
}

echo "Done.";
