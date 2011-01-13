<?php

/**
 * This script scans the 3 basic api_v3 services.ct files (v3_services.ct, v3_services_batch.ct, v3_services_console.ct)
 * and adds permission items objects to the database.
 * Each permission item is associated to the permission names defined in the TAGS section of the services.ct file.
 * Actions allowed for user KS or no KS will also be added to the basic system permissions: USER_SESSION_PERMISSION
 * & ALWAYS_ALLOWED_ACTIONS accordingly.
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

// add deault + plugins permissions
$msg = '***** NOTICE - Starting v3_services.ct';
KalturaLog::notice($msg);
echo $msg.PHP_EOL;
resetServiceConfig();
$serviceConfig = new KalturaServiceConfig('v3_services.ct', null, true);
setPermissions($serviceConfig, true, $userSessionPermission, $noKsPermission);

// add batch partner special permissions
$msg = '***** NOTICE - Starting v3_services_batch.ct';
KalturaLog::notice($msg);
echo $msg.PHP_EOL;
resetServiceConfig();
$serviceConfig = new KalturaServiceConfig('v3_services_batch.ct', null, false, false);
setPermissions($serviceConfig, false, null, null);

// add admin console partner special permissions
$msg = '***** NOTICE - Starting v3_services_console.ct';
KalturaLog::notice($msg);
echo $msg.PHP_EOL;
resetServiceConfig();
$serviceConfig = new KalturaServiceConfig('v3_services_console.ct', null, false, false);
setPermissions($serviceConfig, false, null, null);

$msg = 'Done!';
KalturaLog::notice($msg);
echo $msg.PHP_EOL;


function setPermissions($serviceConfig, $setBaseSystemPermissions, $userSessionPermission, $noKsPermission)
{
	// get list of services defined in the services.ct files
	$services = $serviceConfig->getAllServices();
	
	// for each defined service.action
	foreach ($services as $serviceActionName)
	{
		$serviceConfig->setServiceName($serviceActionName);
		$serviceSplit = explode('.', $serviceActionName);
		$serviceName = $serviceSplit[0];
		$actionName  = $serviceSplit[1];
		$ticketTypes = explode(',', $serviceConfig->getTicketType());
	
		// skip action if set with ticket type N (blocked)
		if (in_array(BLOCKED_TICKET_TYPE, $ticketTypes))
		{
			$msg = '***** NOTICE - Action ['.$serviceActionName.'] is set with ticket type N (blocked) -> skipping!';
			KalturaLog::notice($msg);
			echo $msg.PHP_EOL;
			continue;
		}	
		
		// check if a permission item for the current action already exists
		$c = new Criteria();
		$c->addAnd(kApiActionPermissionItem::SERVICE_COLUMN_NAME, $serviceName, Criteria::EQUAL);
		$c->addAnd(kApiActionPermissionItem::ACTION_COLUMN_NAME, $actionName, Criteria::EQUAL);
		$permissionItem = PermissionItemPeer::doSelectOne($c);
		if ($permissionItem) {
			$msg = '***** NOTICE - Permission item for ['.$serviceActionName.'] already exists with id ['.$permissionItem->getId().']';
			KalturaLog::alert($msg);
			echo $msg.PHP_EOL;
		}
		else {
			// create a new api action permission item and save it
			$permissionItem = new kApiActionPermissionItem();
			$permissionItem->setService($serviceName);
			$permissionItem->setAction($actionName);
			$permissionItem->save();
		}		
		
		// get the defined permission names from the tags section of the services.ct file
		$permissionNames = $serviceConfig->getTags();
		$permissionNames = explode(',', $permissionNames);
		
		$anyPermissionSet = false; // was any permission set to include the current permission item or not
		foreach ($permissionNames as $permissionName)
		{
			if (!$permissionName) {
				continue;
			}
			
			// add the permission item to all its defined permission objects
			$c = new Criteria();
			$c->addAnd(PermissionPeer::NAME, $permissionName, Criteria::EQUAL);
			$c->addAnd(PermissionPeer::TYPE, array(PermissionType::API_ACCESS, PermissionType::EXTERNAL), Criteria::IN);
			$permission = PermissionPeer::doSelectOne($c);
			
			if (!$permission) {
				$msg = '***** ERROR - Permission ['.$permissionName.'] not found in DB although set for ['.$serviceActionName.']';
				KalturaLog::alert($msg);
				echo $msg.PHP_EOL;
				continue;
			}
			
			$permission->addPermissionItem($permissionItem->getId(), true);
			$anyPermissionSet = true;
		}
		
	
		// add permission item to the basic NO_KS and USER_KS permissions according to its ticket type
		// (partner admin role already contains all other permissions)
	
		if ($setBaseSystemPermissions)
		{
			if (in_array(NO_KS_TICKET_TYPE, $ticketTypes))
			{
				$noKsPermission->addPermissionItem($permissionItem->getId(), true);
				$userSessionPermission->addPermissionItem($permissionItem->getId(), true);
				$anyPermissionSet = true;
			}
			else if (in_array(USER_KS_TICKET_TYPE, $ticketTypes))
			{
				$userSessionPermission->addPermissionItem($permissionItem->getId(), true);
				$anyPermissionSet = true;
			}
		}
		
		if (!$anyPermissionSet) {
			$msg = '***** ERROR - No permission was set for ['.$serviceActionName.']';
			KalturaLog::alert($msg);
			echo $msg.PHP_EOL;
		}
	}	
}


function resetServiceConfig()
{
	myServiceConfig::$secondary_config_tables = null;
	myServiceConfig::$path = null;
	myServiceConfig::$strict_mode = null;
	myServiceConfig::$default_config_table = null;
}
