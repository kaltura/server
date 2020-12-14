<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

$filePath = realpath(dirname(__FILE__) . '/../../../') . '/deployment/updates/scripts/ini_files/02_lecture_capture.conversionProfile2.ini';
$ini_item = 'LECTURE_CAPTURE';

$fileName = basename($filePath);
KalturaLog::info("Handling file [$fileName]");
$objectConfigurations = parse_ini_file($filePath, true);

$systemName = $objectConfigurations[$ini_item]['systemName'];

$c = new Criteria();
$c->addAnd(conversionProfile2Peer::SYSTEM_NAME, $systemName);
$conversionProfiles = conversionProfile2Peer::doSelect($c);

foreach ($conversionProfiles as $conversionProfile)
{
    KalturaLog::info("Updating [$systemName] id [{$conversionProfile->getId()}] of partenr [{$conversionProfile->getPartnerId()}]");
    $conversionProfile->setConditionalProfiles($objectConfigurations[$ini_item]['conditionalProfiles']);
    $conversionProfile->setInputTagsMap($objectConfigurations[$ini_item]['InputTagsMap']);
    $conversionProfile->save();
}
