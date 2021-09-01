<?php
/**
 * @package deployment
 */

$addPermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$reachProfileConfig = realpath(dirname(__FILE__)) . '/../../../permissions/service.uiconf.ini';

passthru("php $addPermissionsScript $reachProfileConfig");

