<?php
/**
 * @package deployment
 * @subpackage dragonfly.roles_and_permissions
 *
 * Adds permission to view partner group type
 *
 * No need to re-run after server code deploy
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__) . '../../../../') . '/permissions/object.KalturaPartner.ini';
passthru("php $script $config");