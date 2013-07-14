<?php

error_reporting(E_ALL);

// check for required arguments
if ($argc < 2) {
	die('Configuration file must be set. Please use '.basename(__FILE__).' config_file_name.ini');
}

// verify given ini file exists
$iniFile = $argv[1];
if (!file_exists($iniFile)) {
	die('Cannot find file ['.$iniFile.']');
}

// bootstrap
require_once(dirname(__FILE__).'/../../bootstrap.php');

// load configurations from ini file
$ini = new Zend_Config_Ini($iniFile);

// add new permissions
$oldPermissionsCfg = $ini->permissions;
if ($oldPermissionsCfg) {
	foreach ($oldPermissionsCfg as $permCfg)
	{
		if (is_null($permCfg->partnerId) || $permCfg->partnerId === '') {
			throw new Exception('Permission partner id must be set');
		} else {
			$partnerIds = explode(",", $permCfg->partnerId);
			foreach($partnerIds as $partnerId) 
				removePermission($permCfg, $partnerId);
		}
	}
}

// add new api action permission items
$oldActionItemsCfg = $ini->action_permission_items;
if ($oldActionItemsCfg) {
	foreach ($oldActionItemsCfg as $itemCfg)
	{
		removeActionPermissionItem($itemCfg);
	}
}

// add new api parameters permission items
$oldParameterItemsCfg = $ini->parameter_permission_items;
if ($oldParameterItemsCfg) {
	foreach ($oldParameterItemsCfg as $itemCfg)
	{
		removeParameterPermissionItem($itemCfg);
	}
}

KalturaLog::log('Done');

// ------------------------------------------------------

function removePermission($permissionCfg, $partnerId = null)
{
	// verify obligatory fields
	if (!$permissionCfg->name) {
		throw new Exception('Permission name must be set');
	}
	
	if (is_null($partnerId)) {
		$partnerId = $permissionCfg->partnerId;
	}
	
	// init new db permission object
	$c = new Criteria();
	$c->add(PermissionPeer::NAME, $permissionCfg->name);
	$c->add(PermissionPeer::PARTNER_ID, $partnerId);
	
	$permission = PermissionPeer::doSelectOne($c);
	if(!$permission)
		return;
			
	$permission->setStatus(PermissionStatus::DELETED);
	$permission->save();
}



function removeActionPermissionItem($itemCfg)
{	
	// verify obligatory fields
	if (!$itemCfg->service) {
		throw new Exception('Permission item service must be set');
	}
	if (!$itemCfg->action) {
		throw new Exception('Permission item action must be set');
	}
	if (is_null($itemCfg->partnerId) || $itemCfg->partnerId === '') {
		throw new Exception('Permission item partner id must be set');
	}
	if (is_null($itemCfg->permissions) || $itemCfg->permissions === '') {
		throw new Exception('Permission item permissions must be set');
	}
		
	// check if item already exists in db
	$c = new Criteria();
	$c->addAnd(kApiActionPermissionItem::SERVICE_COLUMN_NAME, strtolower($itemCfg->service));
	$c->addAnd(kApiActionPermissionItem::ACTION_COLUMN_NAME, strtolower($itemCfg->action));
	$c->addAnd(PermissionItemPeer::PARTNER_ID, array(PartnerPeer::GLOBAL_PARTNER, $itemCfg->partnerId), Criteria::IN);
	$c->addAnd(PermissionItemPeer::TYPE, PermissionItemType::API_ACTION_ITEM);
	$permissionItem = PermissionItemPeer::doSelectOne($c);
	
	if(!$permissionItem)
		return;
	
	// add item to each defined permission
	$permissionNames = array_map('trim', explode(',', $itemCfg->permissions));
	removeItemFromPermissions($permissionItem, $permissionNames);
}



function removeParameterPermissionItem($itemCfg)
{
	// verify obligatory fields
	if (!$itemCfg->object) {
		throw new Exception('Permission item object must be set');
	}
	if (!$itemCfg->parameter) {
		throw new Exception('Permission item object parameter must be set');
	}
	if (!$itemCfg->action) {
		throw new Exception('Permission item action id must be set');
	}
	if (is_null($itemCfg->partnerId) || $itemCfg->partnerId === '') {
		throw new Exception('Permission item partner id must be set');
	}
	if (!in_array($itemCfg->action, array(ApiParameterPermissionItemAction::INSERT, ApiParameterPermissionItemAction::READ, ApiParameterPermissionItemAction::UPDATE)))
	{
		throw new Exception("Action type [$itemCfg->action] unknown");
	}
	if (is_null($itemCfg->permissions) || $itemCfg->permissions === '') {
		throw new Exception('Permission item permissions must be set');
	}
	
	
	// check if item already exists in db
	$c = new Criteria();
	$c->addAnd(kApiParameterPermissionItem::OBJECT_COLUMN_NAME, $itemCfg->object);
	$c->addAnd(kApiParameterPermissionItem::PARAMETER_COLUMN_NAME, $itemCfg->parameter);
	$c->addAnd(kApiParameterPermissionItem::ACTION_COLUMN_NAME, $itemCfg->action);
	$c->addAnd(PermissionItemPeer::PARTNER_ID, array(PartnerPeer::GLOBAL_PARTNER, $itemCfg->partnerId), Criteria::IN);
	$c->addAnd(PermissionItemPeer::TYPE, PermissionItemType::API_PARAMETER_ITEM);
	$permissionItem = PermissionItemPeer::doSelectOne($c);
	
	if(!$permissionItem)
		return;
	
	// add item to each defined permission
	$permissionNames = array_map('trim', explode(',', $itemCfg->permissions));
	removeItemFromPermissions($permissionItem, $permissionNames);
}

function removeItemFromPermissions(PermissionItem $item, array $permissionNames)
{
	foreach ($permissionNames as $permissionName)
	{
		$partnerPermission = array_map('trim', explode('>', $permissionName));
		$partnerId = PartnerPeer::GLOBAL_PARTNER;
		if (count($partnerPermission) === 2) {
			$partnerId = trim($partnerPermission[0]);
		}
		$permissionName = trim(end($partnerPermission));
		
		$c = new Criteria();
		$c->addAnd(PermissionPeer::NAME, $permissionName);
		$c->addAnd(PermissionPeer::TYPE, array(PermissionType::NORMAL, PermissionType::PARTNER_GROUP), Criteria::IN);
		$c->addAnd(PermissionPeer::PARTNER_ID, array(PartnerPeer::GLOBAL_PARTNER, $item->getPartnerId(), $partnerId), Criteria::IN);
		$permission = PermissionPeer::doSelectOne($c);
		if(!$permission)
			continue;
		
		$c = new Criteria();
		$c->addAnd(PermissionToPermissionItemPeer::PERMISSION_ITEM_ID, $item->getId());
		$c->addAnd(PermissionToPermissionItemPeer::PERMISSION_ID, $permission->getId());
		$permissionToPermissionItem = PermissionToPermissionItemPeer::doSelectOne($c);
		if(!$permissionToPermissionItem)
			continue;
		
		$permissionToPermissionItem->delete();
	}
}
