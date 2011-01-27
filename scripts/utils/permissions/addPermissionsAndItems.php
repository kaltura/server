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
require_once(dirname(__FILE__).'/../../../alpha/config/sfrootdir.php');
require_once(dirname(__FILE__).'/../../../api_v3/bootstrap.php');
KalturaLog::setLogger(new KalturaStdoutLogger());

DbManager::setConfig(kConf::getDB());
DbManager::initialize();


// load configurations from ini file
$ini = new Zend_Config_Ini($iniFile);

// add new permissions
$newPermissionsCfg = $ini->permissions;
foreach ($newPermissionsCfg as $permCfg)
{
	addPermission($permCfg);
}

// add new api action permission items
$newActionItems = $ini->action_permission_items;
foreach ($newActionItems as $serviceName => $serviceCfg)
{
	foreach ($serviceCfg as $actionName => $permissionNames)
	{
		addActionPermissionItem($serviceName, $actionName, $permissionNames);
	}	
}

// add new api parameters permission items
$newParameterItems = $ini->parameter_permission_items;
foreach ($newParameterItems as $actionType => $actionCfg)
{
	foreach ($actionCfg as $objectName => $objectCfg)
	{
		foreach ($objectCfg as $paramName => $permissionNames)
		{
			addParameterPermissionItem($actionType, $objectName, $paramName, $permissionNames);
		}
	}
}

KalturaLog::log('Done');

// ------------------------------------------------------

function addPermission($permissionCfg)
{
	// name is obligatory
	if (!$permissionCfg->name) {
		throw new Exception('Permission name must be set');
	}
	
	// init new db permission object
	$permission = new Permission();	
	foreach ($permissionCfg as $key => $value)
	{
		$setterCallback = array ( $permission ,"set{$key}");	
		call_user_func_array( $setterCallback , array ($value ) );
	}
	
	if (!$permission->getFriendlyName()) {
		$permission->setFriendlyName($permission->getName());
	}
		
	if (!$permission->getStatus()) {
		$permission->setStatus(PermissionStatus::ACTIVE);
	}
	
	// add to database
	KalturaLog::log('Adding new permission with name ['.$permission->getName().']');
	PermissionPeer::addToPartner($permission, $permission->getPartnerId());
}



function addActionPermissionItem($service, $action, $permissionNames)
{	
	// verify arguments
	$permissionNames = explode(',', $permissionNames);
	if (!$service || !$action || !$permissionNames || count($permissionNames) < 1)
	{
		KalturaLog::alert("Wrong parameters passed service [$service] action [$action] permissionNames [$permissionNames]");
		return;
	}

	// service and action are always kept in lowercase
	$service = strtolower($service);
	$action  = strtolower($action);
	
	// check if item already exists in db
	$c = new Criteria();
	$c->addAnd(kApiActionPermissionItem::SERVICE_COLUMN_NAME, $service, Criteria::EQUAL);
	$c->addAnd(kApiActionPermissionItem::ACTION_COLUMN_NAME, $action, Criteria::EQUAL);
	$c->addAnd(PermissionItemPeer::TYPE, PermissionItemType::API_ACTION_ITEM, Criteria::EQUAL);
	$permissionItem = PermissionItemPeer::doSelectOne($c);
	
	if ($permissionItem)
	{
		KalturaLog::log('Permission item for ['.$service.'->'.$action.'] already exists with id ['.$permissionItem->getId().']');
	}
	else
	{
		// create new permission item object
		$permissionItem = new kApiActionPermissionItem();
		$permissionItem->setService($service);
		$permissionItem->setAction($action);
		$permissionItem->save();
		KalturaLog::log('New permission item id ['.$permissionItem->getId().'] added for ['.$service.'->'.$action.']');
	}
	

	// add item to each defined permission
	foreach ($permissionNames as $permissionName)
	{
		$permissionName = trim($permissionName);
		$c = new Criteria();
		$c->addAnd(PermissionPeer::NAME, $permissionName, Criteria::EQUAL);
		$c->addAnd(PermissionPeer::TYPE, array(PermissionType::API_ACCESS, PermissionType::EXTERNAL, PermissionType::PARTNER_GROUP), Criteria::IN);
		$permission = PermissionPeer::doSelectOne($c);
		
		if (!$permission) {
			KalturaLog::alert('Permission name ['.$permissionName.'] not found in database - skipping!');
			continue;
		}
		
		KalturaLog::log('Adding permission item id ['.$permissionItem->getId().'] to permission id ['.$permission->getId().']');
		$permission->addPermissionItem($permissionItem->getId(), true);
	}	
}

function addParameterPermissionItem($actionType, $objectName, $paramName, $permissionNames)
{
	// verify arguments
	$permissionNames = explode(',', $permissionNames);
	if (!$actionType || !$objectName || !$paramName || !$permissionNames || count($permissionNames) < 1)
	{
		KalturaLog::alert("Wrong parameters passed actionType [$actionType] objectName [$objectName] paramName [$paramName] permissionNames [$permissionNames]");
		return;
	}
	
	if (!in_array($actionType, array(ApiParameterPermissionItemAction::INSERT, ApiParameterPermissionItemAction::READ, ApiParameterPermissionItemAction::UPDATE)))
	{
		KalturaLog::alert("Action type [$actionType] unknown");
		return;
	}
	
	// check if item already exists in db
	$c = new Criteria();
	$c->addAnd(kApiParameterPermissionItem::OBJECT_COLUMN_NAME, $objectName, Criteria::EQUAL);
	$c->addAnd(kApiParameterPermissionItem::PARAMETER_COLUMN_NAME, $paramName, Criteria::EQUAL);
	$c->addAnd(kApiParameterPermissionItem::ACTION_COLUMN_NAME, $actionType, Criteria::EQUAL);
	$c->addAnd(PermissionItemPeer::TYPE, PermissionItemType::API_PARAMETER_ITEM, Criteria::EQUAL);
	$permissionItem = PermissionItemPeer::doSelectOne($c);
	
	if ($permissionItem)
	{
		KalturaLog::log('Permission item for ['.$actionType.'->'.$objectName.'->'.$paramName.'] already exists with id ['.$permissionItem->getId().']');
	}
	else
	{
		// create new permission item object
		$permissionItem = new kApiParameterPermissionItem();
		$permissionItem->setObject($objectName);
		$permissionItem->setParameter($paramName);
		$permissionItem->setAction($actionType);
		$permissionItem->save();
		KalturaLog::log('New permission item id ['.$permissionItem->getId().'] added for ['.$actionType.'->'.$objectName.'->'.$paramName.']');
	}
	

	// add item to each defined permission
	foreach ($permissionNames as $permissionName)
	{
		$permissionName = trim($permissionName);
		$c = new Criteria();
		$c->addAnd(PermissionPeer::NAME, $permissionName, Criteria::EQUAL);
		$c->addAnd(PermissionPeer::TYPE, array(PermissionType::API_ACCESS, PermissionType::EXTERNAL, PermissionType::PARTNER_GROUP), Criteria::IN);
		$permission = PermissionPeer::doSelectOne($c);
		
		if (!$permission) {
			KalturaLog::alert('Permission name ['.$permissionName.'] not found in database - skipping!');
			continue;
		}
		
		KalturaLog::log('Adding permission item id ['.$permissionItem->getId().'] to permission id ['.$permission->getId().']');
		$permission->addPermissionItem($permissionItem->getId(), true);
	}
}
