<?php
ini_set("memory_limit","512M");

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/../../alpha/'));
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       true);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');
require_once(SF_ROOT_DIR.'/../infra/bootstrap_base.php');
require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "batch", "mediaInfoParser", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath('./logs/classMap.cache');
KAutoloader::register();

error_reporting ( E_ALL );

$availModes = array('gensqls', 'execute');

if ($argc < 2)
	die('Usage: ' . basename(__FILE__) . ' <partner id> [<mode: ' . implode('/', $availModes) . '>]' . PHP_EOL);

$partnerId = @$argv[1];
$mode = 'execute';
if ($argc > 2)
	$mode = $argv[2];

if (!in_array($mode, $availModes))
	die('Invalid mode, should be one of ' . implode(',', $availModes) . PHP_EOL);

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();
myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;

$sphinx = new kSphinxSearchManager();

$lastEntryCreatedAt = null;

entryPeer::setUseCriteriaFilter(false);
for (;;)
{
	$c = new Criteria();
	$c->add(entryPeer::PARTNER_ID, $partnerId);
	if ($lastEntryCreatedAt)
		$c->add(entryPeer::CREATED_AT, $lastEntryCreatedAt, Criteria::LESS_EQUAL);
	$c->addDescendingOrderByColumn(entryPeer::CREATED_AT);
	$c->setLimit(500);
	
	$entries = entryPeer::doSelect($c);
	
	foreach($entries as $entry)
	{
		usleep(100);
		if ($mode == 'execute')
		{
			$sphinx->saveToSphinx($entry, false, true);
			echo $entry->getId() . "Saved\n";
		}
		else
		{
			print $sphinx->getSphinxSaveSql($entry, false, true) . PHP_EOL;
		}
		
		$lastEntryCreatedAt = $entry->getCreatedAt(null);
	}
	
    entryPeer::clearInstancePool();

	if (count($entries) < 500)
		break;
}

echo "Done\n";
