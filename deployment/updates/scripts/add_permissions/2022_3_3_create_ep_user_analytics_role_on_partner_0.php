<?php
/**
 * @package deployment
 * @subpackage Rigel.roles_and_permissions
 *
 * Create EP_USER_ANALYTICS role on partner 0
 */

$insertDefaultsScript = realpath(dirname(__FILE__) . '/../../../') . '/base/scripts/insertDefaults.php';
$config = realpath(dirname(__FILE__)) . '/../../../base/scripts/init_data/03.UserRole.ini';

passthru("php $insertDefaultsScript $config");