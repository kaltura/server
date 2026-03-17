<?php
error_reporting(E_ALL);
ini_set("memory_limit", "512M");

chdir(__DIR__);

// Set up a minimal environment for development
if (!defined("KALTURA_ROOT_PATH")) {
    define("KALTURA_ROOT_PATH", realpath(__DIR__ . '/../../'));
}
if (!defined("SF_ROOT_DIR")) {
    define('SF_ROOT_DIR', KALTURA_ROOT_PATH . '/alpha');
}
define("KALTURA_API_V3", true);
define("KALTURA_API_PATH", KALTURA_ROOT_PATH . DIRECTORY_SEPARATOR . "api_v3");

require_once(KALTURA_API_PATH . DIRECTORY_SEPARATOR . 'VERSION.php');

// Set timezone before loading config
date_default_timezone_set('UTC');

// Load config system
require_once(KALTURA_ROOT_PATH . DIRECTORY_SEPARATOR . 'alpha' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'kConf.php');

// Create cache directory if it doesn't exist
$cacheDir = '/tmp/kaltura_cache';
if (!file_exists($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}
if (!file_exists($cacheDir . '/api_v3')) {
    mkdir($cacheDir . '/api_v3', 0755, true);
}

// Autoloader
require_once(KALTURA_ROOT_PATH . DIRECTORY_SEPARATOR . "infra" . DIRECTORY_SEPARATOR . "KAutoloader.php");
KAutoloader::setClassMapFilePath($cacheDir . '/api_v3/classMap.cache');
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "nusoap", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "lib", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "services", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "plugins", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::register();

// Logger
kLoggerCache::InitLogger('generator');
KalturaLog::setContext("API");

// Now generate the XML
$outputPathBase = isset($argv[1]) ? $argv[1] : "/tmp/kaltura_clientlibs";

if (!file_exists($outputPathBase)) {
    mkdir($outputPathBase, 0755, true);
}

$xmlFileName = "$outputPathBase/KalturaClient.xml";

KalturaLog::info("Using code introspection to generate XML schema");
echo "Generating XML schema...\n";

try {
    $xmlGenerator = new XmlClientGenerator();
    $xmlGenerator->generate();

    $files = $xmlGenerator->getOutputFiles();
    file_put_contents($xmlFileName, $files["KalturaClient.xml"]);

    echo "✓ XML generated successfully: $xmlFileName\n";
    echo "\nTo deploy this XML:\n";
    echo "  cp $xmlFileName /opt/kaltura/web/content/clientlibs/\n";
    echo "\nOr specify output path:\n";
    echo "  php generate_xml_dev.php /path/to/output\n";
} catch (Exception $e) {
    echo "✗ Error generating XML: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}