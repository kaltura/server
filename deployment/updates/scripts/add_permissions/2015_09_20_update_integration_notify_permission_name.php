<?php
/**
 * @package deployment
 * 
 * Update integration->notify permission name and related objects
 */
 
$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/partner.0.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.integration.integration.ini';
passthru("php $script $config");

require_once(dirname(__FILE__) . "/../../../../" . "alpha/scripts/bootstrap.php");

$userRole = UserRolePeer::getByNameAndPartnerId(kIntegrationFlowManager::EXTERNAL_INTEGRATION_SERVICES_ROLE_NAME, PartnerPeer::GLOBAL_PARTNER);
$userRole->setPermissionNames("INTEGRATION_ACTIONS");
$userRole->save();

