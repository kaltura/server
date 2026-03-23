<?php
/**
 * @package deployment
 *
 * Deploy video avatar flavor params + conversion profile
 *
 */
require_once (__DIR__ . '/../../bootstrap.php');
$script = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/insertDefaults.php';

$config = realpath(dirname(__FILE__)) . '/ini_files/2026_03_22_source_only.conversionProfile2.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/ini_files/2026_03_22_source_only.flavorParamsConversionProfile.ini';
passthru("php $script $config");
