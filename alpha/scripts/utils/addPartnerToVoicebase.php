<?php
require_once(__dir__ . '/../bootstrap.php');

if($argc < 4)
{
	die("Usage: php " . basename(__FILE__) . " [partner id] [apiKey] [apiPassword] <[transformDfxp]> <[defaultParameters]>" . PHP_EOL);
}

$partnerId = $argv[1];
$apiKey = $argv[2];
$apiPassword = $argv[3];
$transformDfxp = isset($argv[4]) ? (bool)$argv[4] : false;
$defaultParameters = isset($argv[5]) ? $argv[5] : null;

$options = new VoicebaseOptions($apiKey, $apiPassword);
$options->transformDfxp = $transformDfxp;
$options->defaultParams = $defaultParameters;
VoicebasePlugin::setPartnerVoicebaseOptions($partnerId, $options);
	
