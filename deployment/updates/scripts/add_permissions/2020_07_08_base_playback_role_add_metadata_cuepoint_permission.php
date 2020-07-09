<?php
/**
 * @package deployment
 *
 * Add permissions to PLAYBACK BASE ROLE user role:
 * Metadata - add
 * MetadataProfile - list
 * Annotation - add
 * Annotation - update
 */

 
$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.metadata.metadata.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.metadata.metadataprofile.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.annotation.annotation.ini';
passthru("php $script $config");
