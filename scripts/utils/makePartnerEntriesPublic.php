<?php
ini_set("memory_limit","256M");

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/../../alpha/'));
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       true);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');
require_once(SF_ROOT_DIR.'/../infra/bootstrap_base.php');
require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "api_v3", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "batch", "mediaInfoParser", "*"));
KAutoloader::setClassMapFilePath('./logs/classMap.cache');
KAutoloader::register();

error_reporting ( E_ALL );

$dbConf = kConf::getDB ();
DbManager::setConfig ( $dbConf );
DbManager::initialize ();

if (count($argv) !== 1)
{
	die('pleas provide partner id as input' . PHP_EOL . 
		'to run script: ' . __FILE__ . ' X' . PHP_EOL . 
		'whereas X is partner id' . PHP_EOL);
}

if (count($argv) !== 1)
{
	die('please provide a partner id' . PHP_EOL . 
		'to run script: ' . basename(__FILE__) . ' X' . PHP_EOL . 
		'whereas X is partner id' . PHP_EOL);
}
$partner_id = @$argv[1];

$partner = PartnerPeer::retrieveByPK($partner_id);
if(!$partner)
{
        die('no such partner.'.PHP_EOL);
}
$partner->setAppearInSearch(mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK);
$partner->save();

$c = new Criteria();
$c->add(entryPeer::PARTNER_ID, $partner_id);
$c->add(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_PARTNER_ONLY);
$c->addOr(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_NONE);
$c->setLimit(200);

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
$entries = entryPeer::doSelect($c, $con);
echo "number of entries:".count($entries)."\n";
while(count($entries))
{
        foreach($entries as $entry)
        {
                echo "changed DISPLAY_IN_SEARCH for entry: ".$entry->getId()."\n";
                $entry->setDisplayInSearch(mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK);
                $entry->save();
        }
        entryPeer::clearInstancePool();
        $entries = entryPeer::doSelect($c, $con);
}

echo 'Done';
