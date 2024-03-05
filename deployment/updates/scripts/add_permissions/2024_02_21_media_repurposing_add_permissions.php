<?php
/**
 * @package deployment
 * @subpackage tucana.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__) . '/../../../') . '/permissions/service.baseentry.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__) . '/../../../') . '/permissions/service.metadata.metadataprofile.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__) . '/../../../') . '/permissions/service.session.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__) . '/../../../') . '/permissions/service.user.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__) . '/../../../') . '/permissions/service.categoryentry.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__) . '/../../../') . '/permissions/service.flavorasset.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__) . '/../../../') . '/permissions/service.category.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__) . '/../../../') . '/permissions/service.metadata.metadata.ini';
passthru("php $script $config");
