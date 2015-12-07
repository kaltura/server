<?php
/**
 * @package deployment
 * @subpackage jupiter.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
//$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/removePermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.cuepoint.cuepoint.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaCuePoint.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaAdCuePoint.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaAnswerCuePoint.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaThumbCuePoint.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaAnnotationCuePoint.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaCodeCuePoint.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaEventCuePoint.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaQuestionCuePoint.ini';
passthru("php $script $config");