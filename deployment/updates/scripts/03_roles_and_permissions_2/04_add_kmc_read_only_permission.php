<?php

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
$stopFile = dirname(__FILE__).'/stop_role_migration'; // creating this file will stop the script
$roleLimitEachLoop = 20;

//------------------------------------------------------

set_time_limit(0);

require_once(dirname(__FILE__).'/../../../bootstrap.php');

// stores the last handled admin kuser id, helps to restore in case of crash
$lastRoleFile = 'last_role';
$lastRole = 0;
if(file_exists($lastRoleFile)) {
	$lastRole = file_get_contents($lastRoleFile);
	KalturaLog::log('last role file already exists with value - '.$lastRole);
}
if(!$lastRole)
	$lastRole = 0;

$roles = getUserRoles($lastRole, $roleLimitEachLoop);

while(count($roles))
{
	foreach($roles as $role)
	{
		if (file_exists($stopFile)) {
			die('STOP FILE CREATED');
		}

		$lastRole = $role->getId();
		KalturaLog::log('-- role id ' . $lastRole);
		
		$permissions = $role->getPermissionNames();
		$permissions = explode(',', $permissions);
		if (in_array(PermissionName::KMC_ACCESS, $permissions)) {
			if (!in_array(PermissionName::KMC_READ_ONLY, $permissions)) {
				$permissions[] = PermissionName::KMC_READ_ONLY;
				$role->setPermissionNames(implode(',', $permissions));
			}
			else {
				KalturaLog::log('Role ['.$lastRole.'] skipped because it already containsn ['.PermissionName::KMC_READ_ONLY.']');
				continue; // irrelevant role
			}
		}
		else {
			KalturaLog::log('Role ['.$lastRole.'] skipped because it does not contain ['.PermissionName::KMC_ACCESS.']');
			continue; // irrelevant role
		}
		
		
		if (!$dryRun)
		{		
			KalturaLog::log('Saving role id ['.$lastRole.'] with permissions ['.$role->getPermissionNames().']');
			$role->save(); // save
		}
		else
		{
			KalturaLog::log('DRY RUN! - Saving role id ['.$lastRole.'] with permissions ['.$role->getPermissionNames().']');
		}		
				
		file_put_contents($lastRoleFile, $lastRole);
	}
	
	$roles = getUserRoles($lastRole, $roleLimitEachLoop);
}

$msg = 'Done - ' . ($dryRun ? 'DRY RUN!' : 'REAL RUN!');
KalturaLog::log($msg);
echo $msg;

function getUserRoles($lastRole, $roleLimitEachLoop)
{
	UserRolePeer::clearInstancePool();
	$c = new Criteria();
	$c->addAnd(UserRolePeer::ID, $lastRole, Criteria::GREATER_THAN);
	$c->addAscendingOrderByColumn(UserRolePeer::ID);
	$c->setLimit($roleLimitEachLoop);
	UserRolePeer::setUseCriteriaFilter(false);
	$roles = UserRolePeer::doSelect($c);
	UserRolePeer::setUseCriteriaFilter(true);
	return $roles;
}
