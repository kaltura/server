<?php
chdir(dirname(__FILE__));

require_once(__DIR__ . '/../../bootstrap.php');

$entryId = isset($argv[1]) ? $argv[1] : null;
if (!$entryId)
		die('Execute with "php ' . pathinfo(__FILE__, PATHINFO_BASENAME) . ' ENTRY_ID"'.PHP_EOL);

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
$entry = entryPeer::retrieveByPKNoFilter($entryId, $con);
if (!$entry)
	die('Entry id '.$entryId.' was not found'.PHP_EOL);

$sphinx = new kSphinxSearchManager();
KalturaLog::log('Entry id ' . $entry->getId() . ' int id[' . $entry->getIntId() . '] crc id[' . $sphinx->getSphinxId($entry) . '] updated at [' . $entry->getUpdatedAt(null) . ']');
try
{
	$ret = $sphinx->saveToSphinx($entry, true);
}
catch (Exception $e)
{
	KalturaLog::err($e->getMessage());
	exit(-1);
}
exit(0);
