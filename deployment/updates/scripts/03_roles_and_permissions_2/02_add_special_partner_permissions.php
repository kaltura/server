<?php

/**
 * TODO: add documentation
 * ticket type 0 or 1
 * partner group
 */

//-- Bootstraping

error_reporting(E_ALL);

require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(ROOT_DIR . '/api_v3/bootstrap.php');

PermissionPeer::clearInstancePool();
PermissionItemPeer::clearInstancePool();

//-- Script start

// get base system user and no ks permission objects

define('NO_KS_TICKET_TYPE', '0');
define('USER_KS_TICKET_TYPE', '1');
define('BLOCKED_TICKET_TYPE', 'N');

$userSessionPermission  = PermissionPeer::getByNameAndPartner(PermissionName::USER_SESSION_PERMISSION, array(PartnerPeer::GLOBAL_PARTNER));
$noKsPermission         = PermissionPeer::getByNameAndPartner(PermissionName::ALWAYS_ALLOWED_ACTIONS, array(PartnerPeer::GLOBAL_PARTNER));

$userSessionPermissionItemIds = $userSessionPermission->getPermissionItemIds();
$noKsPermissionItemIds = $noKsPermission->getPermissionItemIds();


// special service config files

$serviceConfigFiles = array (
	'v3_services_batch.ct',
	'v3_services_console.ct',
	'v3_services_6028.ct',
	'v3_services_footbo.ct',
	'v3_services-593-21658.ct',
	'v3_services-17291-20772.ct',
	'v3_services-disney-mediabowl.ct',
	'v3_services-epen.ct',
	'v3_services-epen-pppe.ct',
	'v3_services-epen-ppre.ct',
	'v3_services-epen-production.ct',
	'v3_services-epen-pte.ct',
	'v3_services-paramount-mobile.ct',
);


foreach ($serviceConfigFiles as $file)
{
	// init service config for current file
	resetServiceConfig();
	
	$partners = getPartners($file);
	if (!$partners || count($partners) == 0) {
		$msg = '***** NOTICE - No partners found for config file ['.$file.']';
		KalturaLog::notice($msg);
		echo $msg.PHP_EOL;
		continue;
	}
	
	$serviceConfig = new KalturaServiceConfig($file, null, false, false);
	$servicesTable = $serviceConfig->getAllServicesByCt();
	
	// for each defined service.action
	foreach ($servicesTable as $ctPath => $services)
	{	
		foreach ($services as $serviceActionName)
		{
			// get permission item object for the current service/action
			$serviceConfig->setServiceName($serviceActionName);
			$serviceSplit = explode('.', $serviceActionName);
			$serviceName = $serviceSplit[0];
			$actionName  = $serviceSplit[1];
			$ticketTypes = explode(',', $serviceConfig->getTicketType());		
			
			$serviceId = $serviceName;
			$pluginName = getPluginNameFromServicesCtPath($ctPath);
			if ($pluginName) {
				$serviceId = strtolower($pluginName).'_'.$serviceId;
			}
			
			$serviceClass = KalturaServicesMap::getService($serviceId);
			if (!$serviceClass) {
				$tmpServiceIds = KalturaServicesMap::getServiceIdsFromName($serviceName);
				if ($tmpServiceIds && count($tmpServiceIds) == 1)
				{
					$serviceId = reset($tmpServiceIds);
					$serviceClass = KalturaServicesMap::getService($serviceId);
				}
			}
			if (!$serviceClass) {
				$msg = '***** ERROR - service id ['.$serviceId.'] not found in services map!';
				KalturaLog::alert($msg);
				echo $msg.PHP_EOL;
				continue;
			}
			
			// skip action if set with ticket type N (blocked)
			if (in_array(BLOCKED_TICKET_TYPE, $ticketTypes))
			{
				$msg = '***** NOTICE - Action ['.$serviceActionName.'] is set with ticket type N (blocked) -> skipping!';
				KalturaLog::notice($msg);
				echo $msg.PHP_EOL;
				continue;
			}	
			
			$c = new Criteria();
			$c->addAnd(kApiActionPermissionItem::SERVICE_COLUMN_NAME, $serviceId);
			$c->addAnd(kApiActionPermissionItem::ACTION_COLUMN_NAME, $actionName);
			$permissionItem = PermissionItemPeer::doSelectOne($c);
			
			if (!$permissionItem)
			{
				$msg = '***** ERROR - Permission item for service ['.$serviceId.'] action ['.$actionName.'] not found in DB!';
				KalturaLog::alert($msg);
				echo $msg.PHP_EOL;
				continue;
			}
			
			// check if a special ticket type was set for the action which is different from the basic system ticket types
	
			if (in_array(USER_KS_TICKET_TYPE, $ticketTypes) && !in_array($permissionItem->getId(), $userSessionPermissionItemIds))
			{
				// ticket type 1 set - add a special user KS permission to all relevant partners and add current permission item to it
				foreach ($partners as $partner)
				{
					$userKsRole = getOrCreateUserSessionRole($partner->getId());				
					$userKsPermission = getOrCreateSessionPermission($partner->getId(), 'user');
					$userKsPermission->addPermissionItem($permissionItem->getId(), true);
					$userKsRole->setPermissionNames(PermissionName::USER_SESSION_PERMISSION.','.$userKsPermission->getName());
					$partner->setUserSessionRoleId($userKsRole->getId());
					$partner->save();
				}
			}
			
			if (in_array(NO_KS_TICKET_TYPE, $ticketTypes) && !in_array($permissionItem->getId(), $noKsPermissionItemIds))
			{
				// ticket type 0 set - add a special no KS permission to all relevant partners and add current permission item to it
				foreach ($partners as $partner)
				{
					$noKsPermission = getOrCreateSessionPermission($partner->getId(), 'no');
					$noKsPermission->addPermissionItem($permissionItem->getId(), true);
					$currentPerms = $partner->getAlwaysAllowedPermissionNames();
					$currentPerms = explode(',', $currentPerms);
					$currentPerms[] = $noKsPermission->getName();
					$currentPerms = implode(',', $currentPerms);
					$partner->setAlwaysAllowedPermissionNames($currentPerms);
					$partner->save();
				}
			}
			
			// check if partner group is set for the action
			$partnerGroup = $serviceConfig->getPartnerGroup();
			if ($partnerGroup)
			{
				// partner group is set - add a special partner group permission to all relevant partners and add current permission item to it
				foreach ($partners as $partner)
				{
					$partnerGroupPermission = getOrCreatePartnerGroupPermission($partner->getId(), $partnerGroup);
					$partnerGroupPermission->addPermissionItem($permissionItem->getId(), true);				
				}
			}
		}
	}
	
}


$msg = 'Done!';
KalturaLog::notice($msg);
echo $msg.PHP_EOL;


// -- helper functions ------------------------------------------

/**
 * Return all partners with $file set as their SERVICE_CONFIG_ID
 * @param string $file file name
 * @return array of Partner objects
 */
function getPartners($file)
{
	$file = substr($file, 3);
	PartnerPeer::clearInstancePool();
	$c = new Criteria();
	$c->addAnd(PartnerPeer::SERVICE_CONFIG_ID, $file, Criteria::EQUAL);
	$partners = PartnerPeer::doSelect($c);
	return $partners;
}


/**
 * Create a special user/no session permission for given partner id, or get an existing one
 * @param int $partnerId
 * @param string $type should be 'USER' or 'NO'
 */
function getOrCreateSessionPermission($partnerId, $type)
{	
	$permissionName = 'PARTNER_'.$partnerId.'_'.strtoupper($type).'_SESSION_PERMISSION';
	
	PermissionPeer::clearInstancePool();
	$c = new Criteria();
	$c->addAnd(PermissionPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
	$c->addAnd(PermissionPeer::NAME, $permissionName, Criteria::EQUAL);
	$c->addAnd(PermissionPeer::TYPE, PermissionType::API_ACCESS, Criteria::EQUAL);
	
	$permission = PermissionPeer::doSelectOne($c);
	
	if (!$permission) {
		// create permission if not yet created
		$permission = new Permission();
		$permission->setPartnerId($partnerId);
		$permission->setName($permissionName);
		$permission->setFriendlyName('Special '.strtolower($type).' session permission');
		$permission->setDescription('Partner '.$partnerId.' special '.strtolower($type).' session permission');
		$permission->setType(PermissionType::API_ACCESS);
		$permission->setStatus(PermissionStatus::ACTIVE);
		$permission->save();
	}
	
	return $permission;
}


function getOrCreateUserSessionRole($partnerId)
{
	PartnerPeer::clearInstancePool();
	$partner = PartnerPeer::retrieveByPK($partnerId);
	$role = null;
	$id = $partner->getUserSessionRoleId();
	if ($id) {
		$role = UserRolePeer::retrieveByPK($id);
	}
	else {
		$role = new UserRole();
		$role->setPartnerId($partnerId);
		$role->setStatus(UserRoleStatus::ACTIVE);
		$role->setName('Partner '.$partnerId.' user session permission');
		$role->setDescription('Partner '.$partnerId.' user session permission');
		$role->setPermissionNames(PermissionName::USER_SESSION_PERMISSION);
		$role->save();
	}
	return $role;	
}


/**
 * Create a special partner group permission for given partner id, or get an existing one
 * @param int $partnerId
 * @param string $partnerGroup
 */
function getOrCreatePartnerGroupPermission($partnerId, $partnerGroup)
{	
	$permissionName = 'PARTNER_'.$partnerId.'_GROUP_'.$partnerGroup.'_PERMISSION';
	
	PermissionPeer::clearInstancePool();
	$c = new Criteria();
	$c->addAnd(PermissionPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
	$c->addAnd(PermissionPeer::NAME, $permissionName, Criteria::EQUAL);
	$c->addAnd(PermissionPeer::TYPE, PermissionType::PARTNER_GROUP, Criteria::EQUAL);
	
	$permission = PermissionPeer::doSelectOne($c);
	
	if (!$permission) {
		// create permission if not yet created
		$permission = new Permission();
		$permission->setPartnerId($partnerId);
		$permission->setName($permissionName);
		$permission->setFriendlyName('Partner '.$partnerId.' permission for group '.$partnerGroup);
		$permission->setDescription('Partner '.$partnerId.' permission for group '.$partnerGroup);
		$permission->setType(PermissionType::PARTNER_GROUP);
		$permission->setPartnerGroup($partnerGroup);
		$permission->setStatus(PermissionStatus::ACTIVE);
		$permission->save();
	}
	else {
		if ($permission->getPartnerGroup() != $partnerGroup)
		{
			$msg = '***** ERROR - Permission id ['.$permission->getId().'] partner group ['.$permission->getPartnerGroup().'] is different from the required partner group ['.$partnerGroup.']';
			KalturaLog::alert($msg);
			echo $msg.PHP_EOL;
		}
	}
	
	return $permission;
}


function resetServiceConfig()
{
	myServiceConfig::$secondary_config_tables = null;
	myServiceConfig::$path = null;
	myServiceConfig::$strict_mode = null;
	myServiceConfig::$default_config_table = null;
}


function getPluginNameFromServicesCtPath($ctPath)
{
	$pluginClasses = KalturaPluginManager::getPlugins();
	foreach ($pluginClasses as $pluginClass)
	{
		$ct_callback = array ( $pluginClass ,"getServiceConfig"  );
		if (!is_callable($ct_callback)) {
			continue;
		}
		$pluginCtPath = call_user_func($ct_callback);
		if (realpath($pluginCtPath) === realpath($ctPath)) {
			return call_user_func(array($pluginClass, 'getPluginName'));
		}
	}
	return null;
}
