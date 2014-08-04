<?php
/**
 * @package deployment
 *
 * Create playback-only user role
 *
 * No need to re-run after server code deploy
 */

chdir(__DIR__);
require_once (__DIR__ . '/../../bootstrap.php');

$realRun = isset($argv[1]) && $argv[1] == 'realrun';
KalturaStatement::setDryRun(!$realRun);

$userRole = new UserRole();
$userRole->setStrId('PLAYBACK_BASE_ROLE');
$userRole->setName('PLAYBACK BASE ROLE');
$userRole->setSystemName('PLAYBACK_BASE_ROLE');
$userRole->setDescription('Only playback');
$userRole->setPartnerId(0);
$userRole->setStatus(UserRoleStatus::ACTIVE);
$userRole->setPermissionNames('PLAYBACK_BASE_PERMISSION');
$userRole->save();
