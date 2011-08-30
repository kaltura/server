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
	

	
$countLimitEachLoop = 7;
$offset = $countLimitEachLoop;
//------------------------------------------------------


require_once (dirname ( __FILE__ ) . '/../../bootstrap.php');

$con = myDbHelper::getConnection ( myDbHelper::DB_HELPER_CONN_PROPEL2 );
KalturaStatement::setDryRun ( $dryRun );

$c = new Criteria ();
$c->add ( PartnerPeer::ID, 0, Criteria::GREATER_THAN );
$c->setLimit ( $countLimitEachLoop );

$partners = PartnerPeer::doSelect ( $c, $con );

while ( count ( $partners ) ) {
	foreach ( $partners as $partner ) {
		/* @var $partner partner */
		if ($partner->getPartnerPackage() > PartnerPackages::PARTNER_PACKAGE_PAID){
			$partner->setPartnerPackage(PartnerPackages::PARTNER_PACKAGE_PAID);
			$partner->save();
		}elseif ($partner->getPartnerPackage() != PartnerPackages::PARTNER_PACKAGE_FREE && $partner->getPartnerPackage() != PartnerPackages::PARTNER_PACKAGE_PAID){
			$partner->setPartnerPackage(PartnerPackages::PARTNER_PACKAGE_FREE);
			$partner->save();
		}
	}
	
	$c->setOffset($offset);
	PartnerPeer::clearInstancePool();
	$partners = PartnerPeer::doSelect ( $c, $con );
	$offset += $countLimitEachLoop;
	sleep ( 1 );
}


