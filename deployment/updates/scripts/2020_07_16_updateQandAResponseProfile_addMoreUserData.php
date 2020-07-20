<?php
/**
 * @package deployment
 */

define('DEPLOYMENT_DIR', realpath(__DIR__ . '/../..'));

require_once (DEPLOYMENT_DIR . '/bootstrap.php');

$scriptPath = realpath(DEPLOYMENT_DIR . '/../tests/standAloneClient/exec.php');

$templateUpdateXmlPath = realpath(DEPLOYMENT_DIR . '/updates/scripts/xml/2020_07_16_updateQandAResponseProfile_addMoreUserData.template.xml');

if(!file_exists($templateUpdateXmlPath) || !file_exists($scriptPath))
{
    KalturaLog::err('Missing update script file');
    return;
}

passthru("php $scriptPath $templateUpdateXmlPath");
