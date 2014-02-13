<?php
$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../../plugins/scheduled_task/config/permissions.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../../plugins/scheduled_task/plugins/event_notification/config/permissions.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../../plugins/scheduled_task/plugins/metadata/config/permissions.ini';
passthru("php $script $config");