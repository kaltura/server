<?php

/**
 * add sub partner to existing partner group (as adding it to the master partner's group). 
 */

require_once(__DIR__ . '/../bootstrap.php');

if ($argc !== 3) {
	die ( 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . " [sub partner] [master partner]" . PHP_EOL );
}
$subPartnerId = $argv [1];
$masterPartnerId = $argv [2];

$subPartner = PartnerPeer::retrieveByPK ( $subPartnerId );
if (! $subPartner)
	die ( "no such sub partner [$subPartner]." . PHP_EOL );
$masterPartner = PartnerPeer::retrieveByPK ( $masterPartnerId );
if (! $masterPartner)
	die ( "no such master partner [$subPartner]." . PHP_EOL );

PermissionPeer::clearInstancePool ();

$c = new Criteria ();
$c->addAnd ( PermissionPeer::PARTNER_ID, $masterPartner->getId(), Criteria::EQUAL );
$c->addAnd ( PermissionPeer::TYPE, PermissionType::PARTNER_GROUP, Criteria::EQUAL );
$c->addAnd ( PermissionPeer::STATUS, PermissionStatus::ACTIVE, Criteria::EQUAL);
$permission = PermissionPeer::doSelectOne ( $c );

if (! $permission)
	die ("Master partner group doesnot exists" . PHP_EOL);

$group = $permission->getPartnerGroup();
$groupArr = explode(',', $group);
$groupArr[] = $subPartner->getId();
$newGroup = implode(',', $groupArr);

$permission->setPartnerGroup($newGroup);
$permission->save();

echo "current partner group [$newGroup] " . PHP_EOL;
echo "done." . PHP_EOL;


