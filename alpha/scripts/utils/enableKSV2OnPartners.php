<?php
/**
 * Enable KS V2 on partners with EP
 * (With FEATURE_EVENT_PLATFORM_PERMISSION enabled)
 *
 *
 * Examples:
 * php enableKSV2OnPartnersEP.php
 * php enableKSV2OnPartnersEP.php realrun
 */

$dryRun = true;
if (in_array('realrun', $argv))
{
	$dryRun = false;
}

require_once(__DIR__ . '/../../../deployment/bootstrap.php');

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
KalturaStatement::setDryRun($dryRun);

const FEATURE_EVENT_PLATFORM_PERMISSION = 'FEATURE_EVENT_PLATFORM_PERMISSION';

//------------------------------------------------------


$c = new Criteria();
$c->addAnd(PermissionPeer::NAME, FEATURE_EVENT_PLATFORM_PERMISSION, Criteria::EQUAL);
$c->addAnd(PermissionPeer::STATUS, PermissionStatus::ACTIVE, Criteria::EQUAL);
$permissions = PermissionPeer::doSelect($c, $con);

foreach ($permissions as $permission)
{
	$partner = PartnerPeer::retrieveByPK($permission->getPartnerId());
	if (!$partner)
	{
		print("Partner not found for permission id: " . $permission->getId() . "\n");
		continue;
	}

	print("Enabling KS V2 on partner: [" . $partner->getId(). "] \n");

	$partner->setKSVersion(2);
	$partner->save();
}

print("Done\n");
