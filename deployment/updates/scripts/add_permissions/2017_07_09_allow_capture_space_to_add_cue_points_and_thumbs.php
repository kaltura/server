<?php


$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.cuepoint.cuepoint.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.thumbasset.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaFaceCuePoint.ini';
passthru("php $script $config");
