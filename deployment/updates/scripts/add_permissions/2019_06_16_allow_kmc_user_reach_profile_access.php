<?php
/**
 * @package deployment
 * @subpackage orion.roles_and_permissions
 */

$addPermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$reachProfileConfig = realpath(dirname(__FILE__)) . '/../../../permissions/service.reach.reachProfile.ini';

passthru("php $addPermissionsScript $reachProfileConfig");

