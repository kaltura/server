<?php
chdir(__DIR__ . '/../');
require_once 'bootstrap.php';

$peers = array('entry','category');
$availModes = array('gensqls', 'execute');

if ($argc != 3)
	die('Usage: ' . basename(__FILE__) . ' <object id>  [<peer name>: ' . implode('/', $peers) . '>] [<mode: ' . implode('/', $availModes) . '>]' . PHP_EOL);

$objectId = @$argv[1];
$mode = 'execute';
$peer = $argv[2];

if ($argc > 3)
	$mode = $argv[3];

if (!(in_array($peer , $peers)))
	die('Invalid peer name , should be one of ' . implode(',', $peers) . PHP_EOL);
	
if (!in_array($mode, $availModes))
	die('Invalid mode, should be one of ' . implode(',', $availModes) . PHP_EOL);

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$sphinx = new kSphinxSearchManager();

$peerName = (string)$peer.'Peer';

$peerName::setUseCriteriaFilter(false);
$object = $peerName::retrieveByPK($objectId);
if ($object)
{
	if ($mode == 'execute')
	{
		$sphinx->saveToSphinx($object, false, true);
		echo $object->getId() . "Saved\n";
	}
	else
	{
		print $sphinx->getSphinxSaveSql($object, false, true) . PHP_EOL;
	}
}
