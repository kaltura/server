<?php
/**
 * @package deployment
 * @subpackage live.liveParams
 *
 * Add live params
 *
 * No need to re-run after server code deploy
 */

$script = realpath(__DIR__ . '/../../') . '/base/scripts/insertDefaults.php';
$config = realpath(__DIR__ . '/../../') . '/base/scripts/init_data/04.liveParams.ini';
passthru("php $script $config");


$conversionProfileType = 2; // ConversionProfileType::LIVE_STREAM
$additioalFlavorParamsIds = '36,37';
$script = realpath(__DIR__ . '/../../../') . '/alpha/scripts/utils/addAssetParamsToTemplateConversionProfiles.php';
passthru("php $script $conversionProfileType $additioalFlavorParamsIds");
