<?php
/**
 * @package deployment
 * @subpackage jupiter.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.userentry.ini';
passthru("php $script $config");
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.quiz.quizuserentry.ini';
passthru("php $script $config");