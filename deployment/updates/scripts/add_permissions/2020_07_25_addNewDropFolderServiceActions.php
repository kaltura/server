<?php
/**
 * @package deployment
 * Add permissions to reach entry vendor task serve and getServeUrl
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.dropfolder.dropfolder.ini';
passthru("php $script $config");