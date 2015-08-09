<?php
/**
 * @package deployment
 *
 * Create user role for external services
 */

chdir(__DIR__);
require_once (__DIR__ . '/../../bootstrap.php');

$realRun = isset($argv[1]) && $argv[1] == 'realrun';
KalturaStatement::setDryRun(!$realRun);

$userRole = new UserRole();
$userRole->setStrId(IntegrationPlugin::EXTERNAL_INTEGRATION_SERVICES_ROLE_NAME);
$userRole->setName(IntegrationPlugin::EXTERNAL_INTEGRATION_SERVICES_ROLE_NAME);
$userRole->setSystemName(IntegrationPlugin::EXTERNAL_INTEGRATION_SERVICES_ROLE_NAME);
$userRole->setPartnerId(0);
$userRole->setStatus(UserRoleStatus::ACTIVE);
$userRole->setPermissionNames("VOICEBASE_ACTIONS");
$userRole->save();

