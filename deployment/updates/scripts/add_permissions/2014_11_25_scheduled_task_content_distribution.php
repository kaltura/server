<?php
$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../../plugins/scheduled_task/plugins/content_distribution/config/permissions.ini';
passthru("php $script $config");