<?php
/**
 * @package deployment
 * @subpackage kajam.roles_and_permissions
 */

$addPermissionsAndItemsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
//$addPermissionsAndItemsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/removePermissionsAndItems.php';
$insertDefaultsScript = realpath(dirname(__FILE__) . '/../../../') . '/base/scripts/insertDefaults.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/partner.0.ini';
passthru("php $addPermissionsAndItemsScript $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/partner.-2.ini';
passthru("php $addPermissionsAndItemsScript $config");

$config = realpath(dirname(__FILE__)) . '/03.UserRole.ini';
passthru("php $insertDefaultsScript $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.baseentry.ini';
passthru("php $addPermissionsAndItemsScript $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.caption.captionasset.ini';
passthru("php $addPermissionsAndItemsScript $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.cuepoint.cuepoint.ini';
passthru("php $addPermissionsAndItemsScript $config");