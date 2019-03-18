<?php
define('KALTURA_ROOT_PATH', '/opt/kaltura/app');
require_once(KALTURA_ROOT_PATH . '/infra/KAutoloader.php');
define("KALTURA_API_PATH", KALTURA_ROOT_PATH . "/api_v3");
require_once(KALTURA_ROOT_PATH . '/alpha/config/kConf.php');
// Autoloader
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "lib", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "services", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "plugins", "*")); // needed for testmeDoc
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "generator")); // needed for testmeDoc
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/plugins/' . basename(__FILE__) . '.cache');
KAutoloader::register();
require_once(KALTURA_ROOT_PATH . '/vendor/google-api-php-client-1.1.2/src/Google/autoload.php');

// Timezone
date_default_timezone_set(kConf::get("date_default_timezone")); // America/New_York

error_reporting(E_ALL);
KalturaLog::setLogger(new KalturaStdoutLogger());

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

function initClient(KalturaYoutubeApiDistributionProfile $distributionProfile)
{
	$options = array(
		CURLOPT_VERBOSE => true,
		CURLOPT_STDERR => STDOUT,
		CURLOPT_TIMEOUT => 90,
	);

	$client = new Google_Client();
	$client->getIo()->setOptions($options);
	$client->setLogger(new YoutubeApiDistributionEngineLogger($client));
	$client->setClientId($distributionProfile->googleClientId);
	$client->setClientSecret($distributionProfile->googleClientSecret);
	$client->setAccessToken(str_replace('\\', '', $distributionProfile->googleTokenData));

	return $client;
}


if (count($argv) < 3)
{
	echo "Usage: [youtube entry id] [YoutubeApiDistributionProfileId].".PHP_EOL;
	die("Not enough parameters" . "\n");
}

$youtubeEntryId = $argv[1];
$distributionProfileId = $argv[2];
$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
$objectIdentifier = null;
if(!$dbDistributionProfile instanceof YoutubeApiDistributionProfile)
{
	die($dbDistributionProfile . " is not a YoutubeApiDistributionProfile" . "\n");
}

$distributionProfile = new KalturaYoutubeApiDistributionProfile();
$distributionProfile->fromObject($dbDistributionProfile);

$googleClient = initClient($distributionProfile);
$youtube = new Google_Service_YouTube($googleClient);
$statusAnswer = $youtube->videos->listVideos("status", array('id' => $youtubeEntryId));
print_r($statusAnswer);