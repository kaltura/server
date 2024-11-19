<?php
/**
 * enable feature for each partner with exclusions
 * examples:
 * php enablePermissionForPartnersWithExclusions.php 2946661,133402,30832,133302 realrun
 *
 * @package Deployment
 * @subpackage updates
 */

$dryRun = true;
if (in_array ( 'realrun', $argv ))
{
	$dryRun = false;
}

$countLimitEachLoop = 500;
$freePackages = [1,100,101,102,103];
$offset = $countLimitEachLoop;

if ($argc >= 2)
{
	$permissionName = $argv[1];
	$partnersToExclude = explode(',', $argv[2]);
}
else
{
	echo 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . ' {partners_to_exclude} [realrun]' . PHP_EOL;
	die;
}
	

//------------------------------------------------------


require_once(dirname ( __FILE__ ) . '/../../bootstrap.php');

$con = myDbHelper::getConnection ( myDbHelper::DB_HELPER_CONN_PROPEL2 );
KalturaStatement::setDryRun ( $dryRun );

$c = new Criteria();
$c->addAscendingOrderByColumn(PartnerPeer::ID);
$c->addAnd(PartnerPeer::ID, 99, Criteria::GREATER_THAN);
$c->addAnd(PartnerPeer::STATUS,1, Criteria::EQUAL);
$c->setLimit($countLimitEachLoop);
$partners = PartnerPeer::doSelect ( $c, $con );

while (count($partners))
{
	foreach ( $partners as $partner )
	{
		/* @var $partner partner */
		$v2v7Permission = PermissionPeer::getByNameAndPartner(PermissionName::FEATURE_V2_V7_REDIRECT, $partner->getId());
		if (!$v2v7Permission)
		{
			$v2v7Permission = new Permission ();
			$v2v7Permission->setType(PermissionType::SPECIAL_FEATURE);
			$v2v7Permission->setPartnerId($partner->getId());
			$v2v7Permission->setName(PermissionName::FEATURE_V2_V7_REDIRECT);
			$v2v7Permission->setStatus(PermissionStatus::ACTIVE);
			$v2v7Permission->save();
			KalturaLog::debug('Set permission [' . PermissionName::FEATURE_V2_V7_REDIRECT . '] for partner id [' . $partner->getId() . ']');
		}
		elseif ($v2v7Permission->getStatus() != PermissionStatus::ACTIVE)
		{
			$v2v7Permission->setStatus(PermissionStatus::ACTIVE);
			$v2v7Permission->save();
			KalturaLog::debug('Set permission [' . PermissionName::FEATURE_V2_V7_REDIRECT . '] for partner id [' . $partner->getId() . ']');
		}

		if (!$partner->getIsSelfServe() && !in_array($partner->getPartnerPackage(), $freePackages))
		{
			$automationManagerPermission = PermissionPeer::getByNameAndPartner(PermissionName::FEATURE_MEDIA_REPURPOSING_NG_PERMISSION, $partner->getId());
			if (!$automationManagerPermission)
			{
				$automationManagerPermission = new Permission ();
				$automationManagerPermission->setType(PermissionType::SPECIAL_FEATURE);
				$automationManagerPermission->setPartnerId($partner->getId());
				$automationManagerPermission->setName(PermissionName::FEATURE_MEDIA_REPURPOSING_NG_PERMISSION);
				$automationManagerPermission->setStatus(PermissionStatus::ACTIVE);
				$automationManagerPermission->save();
				KalturaLog::debug('Set permission [' . PermissionName::FEATURE_MEDIA_REPURPOSING_NG_PERMISSION . '] for partner id [' . $partner->getId() . ']');
			}
			elseif ($automationManagerPermission->getStatus() != PermissionStatus::ACTIVE)
			{
				$automationManagerPermission->setStatus(PermissionStatus::ACTIVE);
				$automationManagerPermission->save();
				KalturaLog::debug('Set permission [' . PermissionName::FEATURE_MEDIA_REPURPOSING_NG_PERMISSION . '] for partner id [' . $partner->getId() . ']');
			}


			if (!in_array($partner->getId(), $partnersToExclude))
			{
				$teamsPermission = PermissionPeer::getByNameAndPartner('FEATURE_TEAMS_RECORDING_UPLOAD_PERMISSION', $partner->getId());
				if (!$teamsPermission)
				{
					$teamsPermission = new Permission ();
					$teamsPermission->setType(PermissionType::SPECIAL_FEATURE);
					$teamsPermission->setPartnerId($partner->getId());
					$teamsPermission->setName($permissionName);
					$teamsPermission->setStatus(PermissionStatus::ACTIVE);
					$teamsPermission->save();
					KalturaLog::debug('Set permission [FEATURE_TEAMS_RECORDING_UPLOAD_PERMISSION] for partner id [' . $partner->getId() . ']');
				}
				elseif ($teamsPermission->getStatus() != PermissionStatus::ACTIVE)
				{
					$teamsPermission->setStatus(PermissionStatus::ACTIVE);
					$teamsPermission->save();
					KalturaLog::debug('Set permission [FEATURE_TEAMS_RECORDING_UPLOAD_PERMISSION] for partner id [' . $partner->getId() . ']');
				}
			}
		}
	}

	kMemoryManager::clearMemory();
	$c = new Criteria ();
	$c->add (PartnerPeer::STATUS, Partner::PARTNER_STATUS_ACTIVE, Criteria::EQUAL);
	$c->add (PartnerPeer::ID, 99, Criteria::GREATER_EQUAL);
	$c->addAscendingOrderByColumn (PartnerPeer::ID);
	$c->setLimit ($countLimitEachLoop);
	$c->setOffset($offset);

	$partners = PartnerPeer::doSelect ( $c, $con );
	$offset +=  $countLimitEachLoop;
}
print("Done");