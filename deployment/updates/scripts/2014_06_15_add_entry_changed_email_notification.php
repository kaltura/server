<?php
/**
 * @package deployment
 * 
 * Add 'ENTRY_CHANGED' email notification template
 */

$script = realpath(dirname(__FILE__) . '/../../../') . '/tests/standAloneClient/exec.php';
$xmlRequest = realpath(dirname(__FILE__) . '/../../../') . '/tests/standAloneClient/emailEntryChangedNotificationTemplate.xml';
passthru("php $script $xmlRequest");

