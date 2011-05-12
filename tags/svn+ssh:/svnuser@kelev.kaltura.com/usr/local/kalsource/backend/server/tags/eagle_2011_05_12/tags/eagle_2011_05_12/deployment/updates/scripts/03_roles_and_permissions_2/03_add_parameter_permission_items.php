<?php
/**
 * @package deployment
 * @subpackage dragonfly.roles_and_permissions
 * 
 * Adds basic API object parameters that require permissions, to their associated permissions.
 * 
 * Delete from permission_to_permission_item where type = 'kApiParameterPermissionItem' to re-deploy
 */

//-- Bootstraping

error_reporting(E_ALL);

require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(ROOT_DIR . '/api_v3/bootstrap.php');

PermissionPeer::clearInstancePool();
PermissionItemPeer::clearInstancePool();

//-- Script start

// define all items
$permissionItems = array(
	array('object' => 'KalturaBaseEntry', 'parameter' => 'startDate', 'action' => ApiParameterPermissionItemAction::INSERT, 'permission' => PermissionName::CONTENT_MANAGE_SCHEDULE),
	array('object' => 'KalturaBaseEntry', 'parameter' => 'startDate', 'action' => ApiParameterPermissionItemAction::UPDATE, 'permission' => PermissionName::CONTENT_MANAGE_SCHEDULE),
	array('object' => 'KalturaBaseEntry', 'parameter' => 'endDate',   'action' => ApiParameterPermissionItemAction::INSERT, 'permission' => PermissionName::CONTENT_MANAGE_SCHEDULE),
	array('object' => 'KalturaBaseEntry', 'parameter' => 'endDate',   'action' => ApiParameterPermissionItemAction::UPDATE, 'permission' => PermissionName::CONTENT_MANAGE_SCHEDULE),
	array('object' => 'KalturaBaseEntry', 'parameter' => 'accessControlId', 'action' => ApiParameterPermissionItemAction::INSERT, 'permission' => PermissionName::CONTENT_MANAGE_ACCESS_CONTROL),
	array('object' => 'KalturaBaseEntry', 'parameter' => 'accessControlId', 'action' => ApiParameterPermissionItemAction::UPDATE, 'permission' => PermissionName::CONTENT_MANAGE_ACCESS_CONTROL),
	array('object' => 'KalturaBaseEntry', 'parameter' => 'categories', 'action' => ApiParameterPermissionItemAction::INSERT, 'permission' => PermissionName::CONTENT_MANAGE_ASSIGN_CATEGORIES.','.PermissionName::USER_SESSION_PERMISSION),
	array('object' => 'KalturaBaseEntry', 'parameter' => 'categories', 'action' => ApiParameterPermissionItemAction::UPDATE, 'permission' => PermissionName::CONTENT_MANAGE_ASSIGN_CATEGORIES.','.PermissionName::USER_SESSION_PERMISSION),
	array('object' => 'KalturaBaseEntry', 'parameter' => 'categoriesIds', 'action' => ApiParameterPermissionItemAction::INSERT, 'permission' => PermissionName::CONTENT_MANAGE_ASSIGN_CATEGORIES.','.PermissionName::USER_SESSION_PERMISSION),
	array('object' => 'KalturaBaseEntry', 'parameter' => 'categoriesIds', 'action' => ApiParameterPermissionItemAction::UPDATE, 'permission' => PermissionName::CONTENT_MANAGE_ASSIGN_CATEGORIES.','.PermissionName::USER_SESSION_PERMISSION),
	array('object' => 'KalturaBaseEntry', 'parameter' => 'name', 'action' => ApiParameterPermissionItemAction::UPDATE, 'permission' => PermissionName::CONTENT_MANAGE_METADATA.','.PermissionName::USER_SESSION_PERMISSION.','.PermissionName::CONTENT_MODERATE_METADATA),
	array('object' => 'KalturaBaseEntry', 'parameter' => 'tags', 'action' => ApiParameterPermissionItemAction::UPDATE, 'permission' => PermissionName::CONTENT_MANAGE_METADATA.','.PermissionName::USER_SESSION_PERMISSION.','.PermissionName::CONTENT_MODERATE_METADATA),
	array('object' => 'KalturaBaseEntry', 'parameter' => 'description', 'action' => ApiParameterPermissionItemAction::UPDATE, 'permission' => PermissionName::CONTENT_MANAGE_METADATA.','.PermissionName::USER_SESSION_PERMISSION.','.PermissionName::CONTENT_MODERATE_METADATA),	
	array('object' => 'KalturaLiveStreamAdminEntry', 'parameter' => kApiParameterPermissionItem::ALL_VALUES_IDENTIFIER, 'action' => ApiParameterPermissionItemAction::READ, 'permission' => PermissionName::CONTENT_MANAGE_BASE),
	array('object' => 'KalturaLiveStreamAdminEntry', 'parameter' => kApiParameterPermissionItem::ALL_VALUES_IDENTIFIER, 'action' => ApiParameterPermissionItemAction::INSERT, 'permission' => PermissionName::CONTENT_MANAGE_BASE),
	array('object' => 'KalturaLiveStreamAdminEntry', 'parameter' => kApiParameterPermissionItem::ALL_VALUES_IDENTIFIER, 'action' => ApiParameterPermissionItemAction::UPDATE, 'permission' => PermissionName::CONTENT_MANAGE_BASE),
	array('object' => 'KalturaPartner', 'parameter' => 'secret', 'action' => ApiParameterPermissionItemAction::READ, 'permission' => PermissionName::INTEGRATION_BASE),
	array('object' => 'KalturaPartner', 'parameter' => 'adminSecret', 'action' => ApiParameterPermissionItemAction::READ, 'permission' => PermissionName::INTEGRATION_BASE),
);

// add all to required permissions

foreach ($permissionItems as $cur)
{
	$item = new kApiParameterPermissionItem();
	$item->setObject($cur['object']);
	$item->setParameter($cur['parameter']);
	$item->setAction($cur['action']);
	$item->setPartnerId(PartnerPeer::GLOBAL_PARTNER);
	$item->save();
	
	$permissions = $cur['permission'];
	$permissions = explode(',', $permissions);
	
	foreach ($permissions as $permissionName)
	{
		if (!$permissionName) {
			continue;
		}
		$permission = PermissionPeer::getByNameAndPartner(trim($permissionName), array(PartnerPeer::GLOBAL_PARTNER));
		if (!$permission)
		{
			$msg = '***** ERROR - Permission ['.$cur['permission'].'] not found for item ['.$cur['object'].'->'.$cur['parameter'].']';
			KalturaLog::alert($msg);
			echo $msg.PHP_EOL;
			continue;
		}
		$permission->addPermissionItem($item->getId());
		$permission->save();
	}
}
