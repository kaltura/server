<?php
/**
 * @package deployment
 * 
 * Add like->list permission-item
 */

const BASE_USER_ROLE_SYSTEM_NAME = "Basic User Session Role";
const LIKE_LIST_USER_PERMISSION_TO_ADD = "LIKE_LIST_USER";

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/partner.0.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.like.like.ini';
passthru("php $script $config");

require_once(__DIR__ . "/../../../../" . "alpha/scripts/bootstrap.php");

$c = new Criteria();
$c->add(UserRolePeer::SYSTEM_NAME, BASE_USER_ROLE_SYSTEM_NAME);
$c->add(UserRolePeer::PARTNER_ID, PartnerPeer::GLOBAL_PARTNER);

$baseUserRole = UserRolePeer::doSelectOne($c);
$permissionNameStr = $baseUserRole->getPermissionNames();
$permissionNameArr = explode(",", $permissionNameStr);
$permissionNameArr[] = LIKE_LIST_USER_PERMISSION_TO_ADD;
$permissionNameStr = implode(",", $permissionNameArr);
$baseUserRole->setPermissionNames($permissionNameStr);
$baseUserRole->save();
 
