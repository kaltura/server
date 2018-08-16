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

$userRole = new UserRole();
$userRole->setStrId('CAPTIONS_EDITOR_ROLE');
$userRole->setName('Captions editor application role BASE ROLE');
$userRole->setSystemName('CAPTIONS_EDITOR_ROLE');
$userRole->setDescription('Only for captions editor application');
$userRole->setPartnerId(0);
$userRole->setStatus(UserRoleStatus::ACTIVE);
$userRole->setPermissionNames('BASE_USER_SESSION_PERMISSION,CAPTION_MODIFY,PLAYBACK_BASE_PERMISSION');
$userRole->save();