<?php
/**
 * @package deployment
 * @subpackage Ursa.roles_and_permissions
 *
 * Create KMS_RESTRICTED_ROLE role on partner 0
 */

$insertDefaultsScript = realpath(dirname(__FILE__) . '/../../../') . '/base/scripts/insertDefaults.php';
$config = realpath(dirname(__FILE__)) . '/../../../base/scripts/init_data/03.UserRole.ini';

passthru("php $insertDefaultsScript $config");
