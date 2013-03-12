<?php
/**
 * @package deployment
 * @subpackage dragonfly.roles_and_permissions
 *
 * Adds permission to view partner group type
 *
 * No need to re-run after server code deploy
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__) . '../../../../') . '/permissions/service.tagsearch.tag.ini';
passthru("php $script $config");