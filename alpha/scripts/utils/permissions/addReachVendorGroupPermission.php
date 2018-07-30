<?php
require_once(dirname(__FILE__).'/../../bootstrap.php');

if($argc < 2)
{
	die("Usage: php " . basename(__FILE__) . " [partner id]" . PHP_EOL);
}

$partnerId = trim($argv[1]);
if (!PermissionPeer::isValidForPartner(PermissionName::REACH_VENDOR_PARTNER_PERMISSION, $partnerId))
	die("Partner id provided [$partnerId] is not of type Vendor" . PHP_EOL);

$c = new Criteria();
$c->add(PermissionPeer::PARTNER_ID, $partnerId);
$c->add(PermissionPeer::NAME, "REACH_VENDOR_PARTNER_GROUP_*_PERMISSION");
$c->add(PermissionPeer::STATUS, PermissionStatus::ACTIVE);
$existingPermission = PermissionPeer::doSelectOne($c);

if($existingPermission)
	die("Partner id provided [$partnerId] already has required permission" . PHP_EOL);

$permission = new Permission();
$permission->setPartnerId($partnerId);
$permission->setType(PermissionType::PARTNER_GROUP);
$permission->setName("REACH_VENDOR_PARTNER_GROUP_*_PERMISSION");
$permission->setFriendlyName("REACH Vendor group permission");
$permission->setDescription("Reach permission for all partners");
$permission->setDependsOnPermissionNames("REACH_VENDOR_PARTNER_PERMISSION");
$permission->setPartnerGroup("*");
$permission->setStatus(PermissionStatus::ACTIVE);
$permission->save();

echo "Done, created permission with id [{$permission->getId()}]" . PHP_EOL;

$c = new Criteria();
$c->add(PermissionItemPeer::PARAM_1, "reach_entryvendortask");
$c->add(PermissionItemPeer::PARAM_2, "updatejob");
$existingPermissionItem = PermissionItemPeer::doSelectOne($c);
if(!$existingPermissionItem)
	die("Cannot find existing permission item for updatejob action");

$permissionToPermissionItem = new PermissionToPermissionItem();
$permissionToPermissionItem->setPermissionId($permission->getId());
$permissionToPermissionItem->setPermissionItemId($existingPermissionItem->getId());
$permissionToPermissionItem->save();

echo "Done, created permission to permission item with id [{$permissionToPermissionItem->getId()}]" . PHP_EOL;