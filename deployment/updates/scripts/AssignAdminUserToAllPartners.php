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
	

	

require_once (dirname ( __FILE__ ) . '/../../bootstrap.php');

$con = myDbHelper::getConnection ( myDbHelper::DB_HELPER_CONN_PROPEL2 );
KalturaStatement::setDryRun ( $dryRun );

$c = new Criteria ();
$c->add ( UserRolePeer::NAME, "System Administrator", Criteria::EQUAL);

$adminRoles = UserRolePeer::doSelect( $c, $con );
foreach ( $adminRoles as $adminRole) {

	$c = new Criteria ();
	$c->add (KuserToUserRolePeer::USER_ROLE_ID, $adminRole->getId(), Criteria::EQUAL);
	
	$admins =  KuserToUserRolePeer::doSelect($c, $con);
	
	foreach ( $admins as $admin ) {
		$adminUser = $admin->getkuser($con);
		if ($adminUser) {
			$adminUser->setAllowedPartners('*');
			$adminUser->save();
		}
		
	}
}





