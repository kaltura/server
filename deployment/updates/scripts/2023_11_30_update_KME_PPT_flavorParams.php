<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

$filePath = realpath(dirname(__FILE__) . '/../../../') . '/deployment/updates/scripts/ini_files/2023_11_30_kaltura_meetings_documents.ImageFlavorParams.ini';

echo("Handling file: " . basename($filePath));
$objectConfigurations = parse_ini_file($filePath, true);

foreach($objectConfigurations as $ini_item)
{
    $systemName = $ini_item['systemName'];

    $assetParam = assetParamsPeer::retrieveBySystemName($systemName);

    echo("Updating asset [{$assetParam->getId()}], systemName [{$assetParam->getSystemName()}] with operators: " . $ini_item['operators']);

    $assetParam->setOperators($ini_item['operators']);
    $assetParam->save();

}
