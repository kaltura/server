<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . "/../../../tests/standAloneClient/exec.php");

$newTemplateUpdate = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/2019_04_11_updateQNAResponseProfile_addDeleted.xml");

if(!file_exists($newTemplateUpdate) || !file_exists($script))
{
    KalturaLog::err("Missing update script file");
    return;
}

passthru("php $script $newTemplateUpdate");
