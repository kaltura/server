<?php
$script = realpath(dirname(__FILE__) . '/../../../../') . '/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/configs/short_link.ini';
passthru("php $script $config");