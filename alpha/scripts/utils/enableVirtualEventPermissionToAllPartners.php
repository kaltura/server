<?php
/**
 * Enable VIRTUALEVENT_PLUGIN_PERMISSION to all partners
 *
 *
 * Examples:
 * php enableVirtualEventPermissionToAllPartners.php 99 1000
 * php enableVirtualEventPermissionToAllPartners.php 99 1000 realrun
 *
 * @package Deployment
 * @subpackage updates
 */

$dryRun = in_array('realrun', $argv) ? false : true;
if ($argc < 3)
{
	die("Usage: php enableVirtualEventPermissionToAllPartners.php firstPartner lastPartner <realrun>\n");
}
$firstPartner = $argv[1];
$lastPartner = $argv[2];

$countLimitEachLoop = 500;
$offset = $countLimitEachLoop;

const VIRTUALEVENT_PLUGIN_PERMISSION = 'VIRTUALEVENT_PLUGIN_PERMISSION';

//------------------------------------------------------


require_once(__DIR__ . '/../../../deployment/bootstrap.php');

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
KalturaStatement::setDryRun($dryRun);

$c = new Criteria();
$c->addAscendingOrderByColumn(PartnerPeer::ID);
$c->addAnd(PartnerPeer::ID, $firstPartner, Criteria::GREATER_EQUAL);
$c->addAnd(PartnerPeer::STATUS,1, Criteria::EQUAL);
$c->setLimit($countLimitEachLoop);
$partners = PartnerPeer::doSelect($c, $con);

$isLastPartner = false;
while (count($partners))
{
	foreach ($partners as $partner)
	{
		/* @var $partner Partner */
		if ($partner->getId() > $lastPartner)
		{
			$isLastPartner = true;
			break;
		}

		$virtualEventPermission = PermissionPeer::getByNameAndPartner(VIRTUALEVENT_PLUGIN_PERMISSION, $partner->getId());
		if (!$virtualEventPermission)
		{
			$virtualEventPermission = new Permission();
			$virtualEventPermission->setType(PermissionType::SPECIAL_FEATURE);
			$virtualEventPermission->setPartnerId($partner->getId());
			$virtualEventPermission->setName(VIRTUALEVENT_PLUGIN_PERMISSION);
		}
		elseif ($virtualEventPermission->getStatus() == PermissionStatus::ACTIVE)
		{
			continue;
		}
		print("Set permission [" . VIRTUALEVENT_PLUGIN_PERMISSION . "] for partner id [". $partner->getId() ."]\n");
		$virtualEventPermission->setStatus(PermissionStatus::ACTIVE);
		$virtualEventPermission->save();
	}

	if ($isLastPartner)
	{
		break;
	}
	
	kMemoryManager::clearMemory();
	$c = new Criteria();
	$c->addAscendingOrderByColumn(PartnerPeer::ID);
	$c->addAnd(PartnerPeer::ID, 99, Criteria::GREATER_EQUAL);
	$c->addAnd(PartnerPeer::STATUS,1, Criteria::EQUAL);
	$c->setLimit($countLimitEachLoop);
	$c->setOffset($offset);
	
	$partners = PartnerPeer::doSelect($c, $con);
	$offset +=  $countLimitEachLoop;
}

print("Done");
