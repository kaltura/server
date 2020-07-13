<?php

$addPermissionsAndItemsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/partner.0.ini';
passthru("php $addPermissionsAndItemsScript $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaCuePoint.ini';
passthru("php $addPermissionsAndItemsScript $config");

