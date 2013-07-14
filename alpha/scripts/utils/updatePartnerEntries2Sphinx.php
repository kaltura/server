<?php

chdir(__DIR__ . '/../');
require_once(__DIR__ . '/../bootstrap.php');

$availModes = array('gensqls', 'execute');

if ($argc < 2)
	die('Usage: ' . basename(__FILE__) . ' <partner id> [<mode: ' . implode('/', $availModes) . '>] [peer name]' . PHP_EOL);

$partnerId = @$argv[1];
$mode = 'execute';
if ($argc > 2)
	$mode = $argv[2];

$peerName = 'entryPeer';
if ($argc > 3)
	$peerName = $argv[3];

if (!in_array($mode, $availModes))
	die('Invalid mode, should be one of ' . implode(',', $availModes) . PHP_EOL);

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();
myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;

$sphinx = new kSphinxSearchManager();

$lastCreatedAt = null;

$partnerIdField = call_user_func(array($peerName, 'translateFieldName'), 'PartnerId', BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME);
$createdAtField = call_user_func(array($peerName, 'translateFieldName'), 'CreatedAt', BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME);

call_user_func(array($peerName, 'setUseCriteriaFilter'), false);
for (;;)
{
	$c = new Criteria();
	if ($partnerId != -1)
		$c->add($partnerIdField, $partnerId);
	if ($lastCreatedAt)
		$c->add($createdAtField, $lastCreatedAt, Criteria::LESS_EQUAL);
	$c->addDescendingOrderByColumn($createdAtField);
	$c->setLimit(500);
	
	$items = call_user_func(array($peerName, 'doSelect'), $c);
	
	foreach($items as $item)
	{
		usleep(100);
		if ($mode == 'execute')
		{
			echo "Updating [". $item->getId() ."] ... "; 
			$sphinx->saveToSphinx($item, false, true);
			echo "Done\n";
		}
		else
		{
			print $sphinx->getSphinxSaveSql($item, false, true) . ';' . PHP_EOL;
		}
		
		$lastCreatedAt = $item->getCreatedAt(null);
	}
	
	sleep(10);
	
    kMemoryManager::clearMemory();

	if (count($items) < 500)
		break;
}

if ($mode == 'execute')
{
	echo "\nScript Done\n";
}
