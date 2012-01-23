<?php
define('ZEND_AUTOLOADER_PATH', '/var/www/kaltura/web/content/clientlibs/Zend/Loader/StandardAutoloader.php');
define('KALTURA_CLIENT_LIB_PATH', '/var/www/kaltura/web/content/clientlibs/php5zend2/Kaltura');
define('CONFIG_FILE', 'config.ini');

use Kaltura\Client\Configuration as KalturaConfiguration;
use Kaltura\Client\Client as KalturaClient;
use Kaltura\Client\Enum\SessionType as KalturaSessionType;
use Kaltura\Client\ApiException;
use Kaltura\Client\ClientException;

// load zend framework 2
require_once ZEND_AUTOLOADER_PATH;
$loader = new \Zend\Loader\StandardAutoloader();
// register Kaltura namespace
$loader->registerNamespace('Kaltura', KALTURA_CLIENT_LIB_PATH);
$loader->register();

$testerConfig = new \Zend\Config\Ini(dirname(__FILE__).'/'.CONFIG_FILE);

require_once('SampleLoggerImplementation.php'); // FIXME - use autoloader
require_once('Zend2ClientTester.php'); // FIXME - use autoloader

// init kaltura configuration
$config = new KalturaConfiguration($testerConfig->partnerId);
$config->setServiceUrl($testerConfig->serviceUrl);
$config->setCurlTimeout(30);
$config->setLogger(new SampleLoggerImplementation());

// init kaltura client
$client = new KalturaClient($config);

// generate session
$ks = $client->generateSession('59cc61bfe5b0cfd70c8d4fd543423123', $testerConfig->userId, KalturaSessionType::ADMIN, $config->getPartnerId());
$config->getLogger()->log('Kaltura session (ks) was generated successfully: ' . $ks);
$client->setKs($ks);

// check connectivity
try
{
	$client->getSystemService()->ping();
}
catch (ApiException $ex)
{
	$config->getLogger()->log('Ping failed with api error: '.$ex->getMessage());
	die;
}
catch (ClientException $ex)
{
	$config->getLogger()->log('Ping failed with client error: '.$ex->getMessage());
	die;
}

// run the tester
$tester = new Zend2ClientTester($client);
$tester->run();