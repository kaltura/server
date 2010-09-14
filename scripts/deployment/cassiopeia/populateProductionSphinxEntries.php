<?php

set_time_limit(0);

ini_set("memory_limit","700M");

chdir(dirname(__FILE__));

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../../'));
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "metadata", "*"));
KAutoloader::setClassMapFilePath('../../cache/classMap.cache');
KAutoloader::register();

date_default_timezone_set(kConf::get("date_default_timezone")); // America/New_York

error_reporting(E_ALL);

$dbConf = array (
  'datasources' => 
  array (
    'default' => 'propel',
  
    'propel' => 
    array (
      'adapter' => 'mysql',
      'connection' => 
      array (
      	'classname' => 'KalturaPDO',
        'phptype' => 'mysql',
        'database' => 'kaltura',
        'hostspec' => '192.168.192.4',
        'user' => 'kaltura',
        'password' => 'kaltura',
		'dsn' => 'mysql:host=192.168.192.4;dbname=kaltura;user=kaltura;password=kaltura;',
      ),
    ),
    
  
    'propel2' => 
    array (
      'adapter' => 'mysql',
      'connection' => 
      array (
      	'classname' => 'KalturaPDO',
        'phptype' => 'mysql',
        'database' => 'kaltura',
        'hostspec' => '192.168.192.4',
        'user' => 'kaltura_read',
        'password' => 'kaltura_read',
		'dsn' => 'mysql:host=192.168.192.4;dbname=kaltura;user=kaltura_read;password=kaltura_read;',
      ),
    ),
    
  
    'propel3' => 
    array (
      'adapter' => 'mysql',
      'connection' => 
      array (
      	'classname' => 'KalturaPDO',
        'phptype' => 'mysql',
        'database' => 'kaltura',
        'hostspec' => '192.168.192.4',
        'user' => 'kaltura_read',
        'password' => 'kaltura_read',
		'dsn' => 'mysql:host=192.168.192.4;dbname=kaltura;user=kaltura_read;password=kaltura_read;',
      ),
    ),
  ),
  'log' => 
  array (
    'ident' => 'kaltura',
    'level' => '7',
  ),
);

KalturaLog::setLogger(new KalturaStdoutLogger());

//$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$exclude = array(
	396894, 
	403598, 
	765735, 
	776915, 
	795308, 
	913844, 
	961914, 
	968615, 
	1045770, 
	1079049, 
	1079053, 
	1079055, 
	1079085, 
	1079097, 
	1079101, 
	1079106, 
	1079113, 
	1080762, 
	1080763, 
	1080766, 
	1080767, 
	1080768, 
	1080770, 
	1080898, 
	1080902, 
	1114125, 
	1114126, 
	1114127, 
	1114128, 
	1114138, 
	1114139, 
	1114140, 
	1114141, 
	28466202, 
);

$lastEntryFile = 'last_entry';
$lastTimeFile = 'last_time';

$lastEntry = 0;
$lastTime = 0;

if(file_exists($lastEntryFile))
	$lastEntry = file_get_contents($lastEntryFile);
if(file_exists($lastTimeFile))
	$lastTime = file_get_contents($lastTimeFile);

if(!$lastEntry)
	$lastEntry = 0;
if(!$lastTime)
	$lastTime = 0;


if($argc > 1 && is_numeric($argv[1]))
	$lastEntry = max($lastEntry, $argv[1]);
	
$c = new Criteria();

$critId = $c->getNewCriterion(entryPeer::INT_ID, $lastEntry, Criteria::GREATER_THAN);	
$critTime = $c->getNewCriterion(entryPeer::UPDATED_AT, $lastTime, Criteria::GREATER_EQUAL);
$critId->addOr($critTime);

$c->addAnd($critId);
$c->addAscendingOrderByColumn(entryPeer::INT_ID);
$c->setLimit(10000);

$insertsFile = fopen('inserts.sql', 'a');
$errorsFile = fopen('errors.sql', 'a');

if(!$insertsFile)
{
	echo "upable to open sql file [" . realpath($insertsFile) . "]";
	exit;
}

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);

//$c->add(entryPeer::INT_ID, implode(',', $exclude), Criteria::NOT_IN);
$entries = entryPeer::doSelect($c, $con);
while(count($entries))
{
	$currentEntry = null;
	foreach($entries as $entry)
	{
		$currentEntry = $entry->getIntId();
		if(in_array($currentEntry, $exclude))
		{
			fputs($errorsFile, "Entry [" . $entry->getId() . "] field int_id is dullicated [$currentEntry]\n");
			continue;
		}
		
		KalturaLog::log('entry id ' . $entry->getId() . ' int id[' . $currentEntry . ']');
		
		try{
			$isInsert = false;
			if($currentEntry > $lastEntry)
				$isInsert = true;
				
			$sql = $entry->getSphinxSaveSql($isInsert, true);
			
			fputs($insertsFile, "$sql;\n");
		}
		catch(Exception $e){
			fputs($errorsFile, $e->getMessage() . "\n");
		}
		
		file_put_contents($lastEntryFile, $currentEntry);
		file_put_contents($lastTimeFile, time());
	}
	$lastEntry = $currentEntry;
	
	$c->setOffset($c->getOffset() + count($entries));
	entryPeer::clearInstancePool();
	MetadataPeer::clearInstancePool();
	MetadataProfilePeer::clearInstancePool();
	MetadataProfileFieldPeer::clearInstancePool();
	$entries = entryPeer::doSelect($c, $con);
}

fclose($insertsFile);
fclose($errorsFile);
KalturaLog::log('Done');