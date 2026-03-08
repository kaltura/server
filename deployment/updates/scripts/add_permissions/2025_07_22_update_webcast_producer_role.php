<?php

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionToRole.php';
passthru("php $script 0 'WEbcast producer device role' 'ANALYTICS_BASE' realrun");
