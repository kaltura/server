<?php
/**
 * @package deployment
 * @subpackage live.liveStream
 *
 * Create email-notification and custom-data profile on partner 99
 *
 * No need to re-run after server code deploy
 */

chdir(__DIR__);
require_once (__DIR__ . '/../../bootstrap.php');

$realRun = isset($argv[1]) && $argv[1] == 'realrun';
KalturaStatement::setDryRun(!$realRun);

$userRole = new UserRole();
$userRole->setStrId('NO_SESSION_ROLE');
$userRole->setName('No Session');
$userRole->setSystemName('No Session');
$userRole->setDescription('Only always allowed actions');
$userRole->setPartnerId(0);
$userRole->setStatus(UserRoleStatus::ACTIVE);
$userRole->setPermissionNames('ALWAYS_ALLOWED_ACTIONS,ALWAYS_ALLOWED_PERMISSION_HYBRID_ECDN');
$userRole->save();
