<?php
/**
 * @package deployment
 * @subpackage mercury.roles_and_permissions
 *
 * Add update start time API to cue point
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.cuepoint.cuepoint.ini';
passthru("php $script $config");