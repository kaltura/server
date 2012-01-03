<?php
/**
 * enable feature for each partner
 * set to all partners with partner->partnerPackage > 1 to 1  
 * @package Deployment
 * @subpackage updates
 */

$dryRun = true; //TODO: change for real run
if (in_array ( 'realrun', $argv ))
	$dryRun = false;
	

	
$countLimitEachLoop = 100;
$offset = $countLimitEachLoop;
//------------------------------------------------------


require_once (dirname ( __FILE__ ) . '/../../../bootstrap.php');

$con = myDbHelper::getConnection ( myDbHelper::DB_HELPER_CONN_PROPEL2 );
KalturaStatement::setDryRun ( $dryRun );

$c = new Criteria ();
$c->add ( PermissionPeer::NAME, 'dropFolder.CONTENT_INGEST_DROP_FOLDER_MATCH', Criteria::EQUAL );
$c->addAnd(PermissionPeer::TYPE, PermissionType::SPECIAL_FEATURE, Criteria::EQUAL);
$c->addAnd(PermissionPeer::PARTNER_ID, 0, Criteria::NOT_EQUAL);
$c->setLimit ( $countLimitEachLoop );

$permissions = PermissionPeer::doSelect ( $c, $con );


while ( count ( $permissions ) ) {
	foreach ( $permissions as $permission ) {
		
			$permission->setName('CONTENT_INGEST_DROP_FOLDER_MATCH');
			$permission->setDependsOnPermissionNames('DROPFOLDER_PLUGIN_PERMISSION');
			$permission->save();
		
	}
	
	$c->setOffset($offset);
	PermissionPeer::clearInstancePool();
	$permissions = PermissionPeer::doSelect ( $c, $con );
	$offset += $countLimitEachLoop;
	sleep ( 1 );
}

$c = new Criteria ();
$c->add ( UserRolePeer::PERMISSION_NAMES, "%dropFolder.CONTENT_INGEST_DROP_FOLDER_MATCH%" , Criteria::LIKE ); 
$c->setLimit ( $countLimitEachLoop );

$userRoles = UserRolePeer::doSelect ( $c, $con );

while ( count ( $userRoles ) ) {
	foreach ( $userRoles as $userRole ) {
			$partnerId = $userRole->getPartnerId();
		
			PermissionPeer::setUseCriteriaFilter(false);
			$permission = PermissionPeer::getByNameAndPartner('CONTENT_INGEST_DROP_FOLDER_MATCH', array($partnerId));
			PermissionPeer::setUseCriteriaFilter(true);
			if(!$permission)	
			{
				$permission = new Permission();
				
				$permission->setName('CONTENT_INGEST_DROP_FOLDER_MATCH');
				$permission->setDependsOnPermissionNames('DROPFOLDER_PLUGIN_PERMISSION');
				$permission->setType(PermissionType::SPECIAL_FEATURE);
				$permission->setPartnerId($partnerId);
				$permission->setStatus(PermissionStatus::ACTIVE);
		
				// add to database
				KalturaLog::log('Adding new permission with name ['.$permission->getName().'] to partner id ['.$permission->getPartnerId().']');
				
				PermissionPeer::addToPartner($permission, $permission->getPartnerId());		
			}
	}
	
	$c->setOffset($offset);
	UserRolePeer::clearInstancePool();
	$userRoles = UserRolePeer::doSelect ( $c, $con );
	$offset += $countLimitEachLoop;
	sleep ( 1 );
}

$script = realpath(dirname(__FILE__) . '/../../../../') . '/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../../plugins/drop_folder/config/drop_folder_permissions.ini';
passthru("php $script $config");


