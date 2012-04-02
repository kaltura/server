<?php
/* set this path to the location of Zend/Loader/StandardAutoloader.php, 
 * the client library can be used with any other php5.3 namespace style autoloaders (for example symfony2 & doctrine2)
*/
define('ZEND_AUTOLOADER_PATH', '/_path_/_to_/_zend_/_framework_/Zend/Loader/StandardAutoloader.php');
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
$loader->registerNamespace('Kaltura', dirname(__FILE__).'/../library/Kaltura');
$loader->registerNamespace('Test', dirname(__FILE__).'/Test');
$loader->register();

$testerConfig = parse_ini_file(dirname(__FILE__).'/'.CONFIG_FILE);

if ($testerConfig['partnerId'] == 123)
	die('Please edit the configuration file (config.ini)');
	
// init kaltura configuration
$config = new KalturaConfiguration($testerConfig['partnerId']);
$config->setServiceUrl($testerConfig['serviceUrl']);
$config->setCurlTimeout(30);
$config->setLogger(new \Test\SampleLoggerImplementation());

// init kaltura client
$client = new KalturaClient($config);

// generate session
$ks = $client->generateSession($testerConfig['adminSecret'], $testerConfig['userId'], KalturaSessionType::ADMIN, $config->getPartnerId());
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
$tester = new \Test\Zend2ClientTester($client);
$tester->run();