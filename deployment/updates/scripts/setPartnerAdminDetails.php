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


require_once (dirname ( __FILE__ ) . '/../../bootstrap.php');

$con = myDbHelper::getConnection ( myDbHelper::DB_HELPER_CONN_PROPEL2 );
KalturaStatement::setDryRun ( $dryRun );

$c = new Criteria ();
$c->add ( PartnerPeer::ID, 0, Criteria::GREATER_THAN );
$c->add ( PartnerPeer::ADMIN_EMAIL, null, Criteria::ISNULL );
$c->setLimit ( $countLimitEachLoop );

$partners = PartnerPeer::doSelect ( $c, $con );

while ( count ( $partners ) ) {
	foreach ( $partners as $partner ) {
		$kuserAccountOwner = $partner->getAccountOwnerKuserId();
		if (!$kuserAccountOwner) {
			KalturaLog::err('ERROR - Cannot find account owner kuser id for partner ['.$partner->getId().']');
		} else {
			$c = new Criteria ();
			$c->add ( kuserPeer::ID, $kuserAccountOwner, Criteria::EQUAL );
	
			$kuser = kuserPeer::doSelectOne($c, $con);
			if (!$kuser) {
				KalturaLog::err('ERROR - Cannot find kuser with id ['.$kuserAccountOwner.']');
			} else {
				$partner->setAdminName($kuser->getFullName());
				$partner->setAdminEmail($kuser->getEmail());
				$partner->save();
				KalturaLog::log('Update partner ['.$partner->getId().'] set admin name ['.$kuser->getFullName().'] admin email ['.$kuser->getEmail().']');
			}
		}
	}
	$c->setOffset($offset);
	PartnerPeer::clearInstancePool();
	$partners = PartnerPeer::doSelect ( $c, $con );
	$offset += $countLimitEachLoop;
	sleep ( 1 );
}


