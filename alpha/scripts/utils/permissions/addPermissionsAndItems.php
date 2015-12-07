<?php

error_reporting(E_ALL);

// check for required arguments
if ($argc < 2) 
{
	echo "Configuration file must be set.\n";
	exit(-1);
}

// verify given ini file exists
$iniFile = $argv[1];
if (!file_exists($iniFile)) 
{
	echo "Cannot find file [$iniFile]\n";
	exit(-1);
}

// bootstrap
require_once(dirname(__FILE__).'/../../bootstrap.php');

// load configurations from ini file
$ini = new Zend_Config_Ini($iniFile);

// add new permissions
$newPermissionsCfg = $ini->permissions;
if ($newPermissionsCfg) {
	foreach ($newPermissionsCfg as $permCfg)
	{
		addPermission($permCfg);
	}
}

// add new api action permission items
$newActionItemsCfg = $ini->action_permission_items;
if ($newActionItemsCfg) {
	foreach ($newActionItemsCfg as $itemCfg)
	{
		addActionPermissionItem($itemCfg);
	}
}


// add new api parameters permission items
$newParameterItemsCfg = $ini->parameter_permission_items;
if ($newParameterItemsCfg) {
	foreach ($newParameterItemsCfg as $itemCfg)
	{
		addParameterPermissionItem($itemCfg);
	}
}

KalturaLog::log('Done');

// ------------------------------------------------------

function addPermission($permissionCfg)
{
	// verify obligatory fields
	if (!$permissionCfg->name) {
		throw new Exception('Permission name must be set');
	}
	if ((is_null($permissionCfg->partnerId) || $permissionCfg->partnerId === '') &&
		(is_null($permissionCfg->partnerPackages) ||  $permissionCfg->partnerPackages === '' )){
		throw new Exception('Permission partner id or partner package must be set');
	}
	
	if (isset($permissionCfg->partnerId) && $permissionCfg->partnerId != '') {
		$partnerIds = explode(",", $permissionCfg->partnerId);
		foreach($partnerIds as $partnerId) 
			addPermissionToPartner($permissionCfg, $partnerId);
	}
		
		
	if (isset($permissionCfg->partnerPackages) &&  $permissionCfg->partnerPackages != '' )
	{
		$countLimitEachLoop = 100;
		$offset = $countLimitEachLoop;
			
		$c = new Criteria();
		$c->add(PartnerPeer::ID, 0, Criteria::GREATER_THAN);
		$c->add(PartnerPeer::PARTNER_PACKAGE, explode(',',$permissionCfg->partnerPackages), Criteria::IN);
		$c->setLimit($countLimitEachLoop);
		
		$partners = PartnerPeer::doSelect($c);
		
		while(count($partners)) 
		{
			foreach($partners as $partner)
				addPermissionToPartner($permissionCfg, $partner->getId());
				
			$c->setOffset($offset);
			PartnerPeer::clearInstancePool();
			$partners = PartnerPeer::doSelect($c);
			$offset += $countLimitEachLoop;
			sleep(1);
		}
	}
}

function addPermissionToPartner($permissionCfg, $partnerId = null){
	// init new db permission object
	if (is_null($partnerId))
		$partnerId = $permissionCfg->partnerId;
	
	PermissionPeer::setUseCriteriaFilter(false);
	$permission = PermissionPeer::getByNameAndPartner($permissionCfg->name, $partnerId);
	PermissionPeer::setUseCriteriaFilter(true);
	if(!$permission)	
		$permission = new Permission();
			
	foreach ($permissionCfg as $key => $value)
	{
		if($key == 'partnerPackages')
			continue;
			
		$setterCallback = array ( $permission ,"set{$key}");	
		call_user_func_array( $setterCallback , array ($value ) );
	}
	
	if (!$permission->getFriendlyName())
		$permission->setFriendlyName($permission->getName());
	
	if ($partnerId != null)
		$permission->setPartnerId($partnerId);
		
	$permission->setStatus(PermissionStatus::ACTIVE);
	
	// add to database
	KalturaLog::log('Adding new permission with name ['.$permission->getName().'] to partner id ['.$permission->getPartnerId().']');
	try {
		if($permission->getId())
			$permission->save();
		else
			PermissionPeer::addToPartner($permission, $permission->getPartnerId());		
	}
	catch (kPermissionException $e)	{
		if ($e->getCode() === kPermissionException::PERMISSION_ALREADY_EXISTS) {
			KalturaLog::log('Permission name ['.$permission->getName().'] already exists for partner id ['.$permission->getPartnerId().']');
		}
		else {
			throw $e;
		}
	}
}

function addActionPermissionItem($itemCfg)
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
		
	// check if item already exists in db
	$c = new Criteria();
	$c->addAnd(kApiActionPermissionItem::SERVICE_COLUMN_NAME, strtolower($itemCfg->service), Criteria::EQUAL);
	$c->addAnd(kApiActionPermissionItem::ACTION_COLUMN_NAME, strtolower($itemCfg->action), Criteria::EQUAL);
	$c->addAnd(PermissionItemPeer::PARTNER_ID, array(PartnerPeer::GLOBAL_PARTNER, $itemCfg->partnerId), Criteria::IN);
	$c->addAnd(PermissionItemPeer::TYPE, PermissionItemType::API_ACTION_ITEM, Criteria::EQUAL);
	$existingItem = PermissionItemPeer::doSelectOne($c);
	
	$item = null;
	if ($existingItem)
	{
		$item = $existingItem;
		KalturaLog::log('Permission item for ['.$item->getService().'->'.$item->getAction().'] partner id ['.$item->getPartnerId().'] already exists with id ['.$existingItem->getId().']');
	}
	else
	{
		// save new permission item object
		$item = new kApiActionPermissionItem();	
		foreach ($itemCfg as $key => $value)
		{
			if ($key === 'permissions') {
				continue; // permissions are set later
			}
					
			$setterCallback = array ( $item ,"set{$key}");	
			call_user_func_array( $setterCallback , array ($value ) );
		}
		// service and action are always kept in lowercase
		$item->setService(strtolower($item->getService()));
		$item->setAction(strtolower($item->getAction()));
		$item->save();
		KalturaLog::log('New permission item id ['.$item->getId().'] added for ['.$item->getService().'->'.$item->getAction().'] partner id ['.$item->getPartnerId().']');
	}
	
	// add item to each defined permission
	$permissionNames = array_map('trim', explode(',', $itemCfg->permissions));
	addItemToPermissions($item, $permissionNames, $itemCfg->partnerId);
	
}



function addParameterPermissionItem($itemCfg)
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
	if (!in_array($itemCfg->action, array(ApiParameterPermissionItemAction::INSERT, ApiParameterPermissionItemAction::READ, ApiParameterPermissionItemAction::UPDATE, ApiParameterPermissionItemAction::USAGE)))
	{
		throw new Exception("Action type [$itemCfg->action] unknown");
	}
	
	
	// check if item already exists in db
	$c = new Criteria();
	$c->addAnd(kApiParameterPermissionItem::OBJECT_COLUMN_NAME, $itemCfg->object, Criteria::EQUAL);
	$c->addAnd(kApiParameterPermissionItem::PARAMETER_COLUMN_NAME, $itemCfg->parameter, Criteria::EQUAL);
	$c->addAnd(kApiParameterPermissionItem::ACTION_COLUMN_NAME, $itemCfg->action, Criteria::EQUAL);
	$c->addAnd(PermissionItemPeer::PARTNER_ID, array(PartnerPeer::GLOBAL_PARTNER, $itemCfg->partnerId), Criteria::IN);
	$c->addAnd(PermissionItemPeer::TYPE, PermissionItemType::API_PARAMETER_ITEM, Criteria::EQUAL);
	$existingItem = PermissionItemPeer::doSelectOne($c);
	
	$item = null;
	if ($existingItem)
	{
		$item = $existingItem;
		KalturaLog::log('Permission item for ['.$item->getAction().'->'.$item->getObject().'->'.$item->getParameter().'] partner id ['.$item->getPartnerId().'] already exists with id ['.$item->getId().']');
	}
	else
	{
		// save new permission item object
		$item = new kApiParameterPermissionItem();	
		foreach ($itemCfg as $key => $value)
		{
			if ($key === 'permissions') {
				continue; // permissions are set later
			}
					
			$setterCallback = array ( $item ,"set{$key}");	
			if (method_exists($item,'set'.$key)){
			    call_user_func_array( $setterCallback , array ($value ) );
			}else{
			    KalturaLog::err("Skipping call to set$key() since there is no such method.");
			}
		}
		$item->save();
		KalturaLog::log('New permission item id ['.$item->getId().'] added for ['.$item->getAction().'->'.$item->getObject().'->'.$item->getParameter().'] partner id ['.$item->getPartnerId().']');
	}
	
	// add item to each defined permission
	$permissionNames = array_map('trim', str_getcsv($itemCfg->permissions));
	addItemToPermissions($item, $permissionNames, $itemCfg->partnerId);
}

function addItemToPermissions($item, $permissionNames, $partnerId)
{
	foreach ($permissionNames as $permissionName)
	{
		PermissionToPermissionItemPeer::clearInstancePool();
		
		$partnerPermission = array_map('trim', explode('>', $permissionName));
		if (count($partnerPermission) === 2)
			$partnerId = trim($partnerPermission[0]);
			
		$permissionName = trim(end($partnerPermission));
		
		$c = new Criteria();
		$c->addAnd(PermissionPeer::NAME, $permissionName, Criteria::EQUAL);
		$c->addAnd(PermissionPeer::TYPE, array(PermissionType::NORMAL, PermissionType::PARTNER_GROUP), Criteria::IN);
		$c->addAnd(PermissionPeer::PARTNER_ID, array(PartnerPeer::GLOBAL_PARTNER, $item->getPartnerId(), $partnerId), Criteria::IN);
		$permission = PermissionPeer::doSelectOne($c);
		
		if (!$permission) {
			KalturaLog::alert('ERROR - Permission name ['.$permissionName.'] for partner ['.$item->getPartnerId().'] not found in database - skipping!');
			continue;
		}
		
		KalturaLog::log('Adding permission item id ['.$item->getId().'] to permission id ['.$permission->getId().']');
		$permission->addPermissionItem($item->getId(), true);
	}
}
