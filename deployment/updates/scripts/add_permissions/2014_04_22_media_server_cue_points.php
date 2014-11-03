<?php
/**
 * @package deployment
 * @subpackage ix.roles_and_permissions
 *
 * Enable media-server list cue-points
 *
 * No need to re-run after server code deploy
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__) . '/../../../') . '/permissions/partner.-5.ini';
passthru("php $script $config");

