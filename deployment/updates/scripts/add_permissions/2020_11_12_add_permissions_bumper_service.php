<?php
/**
 * @package deployment
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.bumper.bumper.ini';
passthru("php $script $config");
