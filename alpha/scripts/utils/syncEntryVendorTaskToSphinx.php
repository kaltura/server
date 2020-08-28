<?php
require_once (dirname(__FILE__) . '/../bootstrap.php');

if ($argc < 2)
{
	echo "\n ========= Sync Entry Vendor Task To Sphinx ========= \n";
	die (" Missing required parameters:\n php " . $argv[0] . " {entryVendorTask || entryVendorTask_1,entryVendorTask_2,.. || path/to/entryVendorTaskList.txt} [realrun / dryrun]\n\n");
}

if(is_file($argv[1]))
{
	$entryVendorTaskList = file($argv[1]) or die ('Could not read file from path: ' . $argv[1] . PHP_EOL);
}
elseif (strpos($argv[1], ','))
{
	$entryVendorTaskList = explode(',', $argv[1]);
}
else
{
	$entryVendorTaskList = array($argv[1]);
}

$dryRun = true;
if ($argc == 3 && $argv[2] == 'realrun')
{
	$dryRun = false;
}

KalturaStatement::setDryRun($dryRun);
KalturaLog::debug('dryrun value: ['.$dryRun.']');
KalturaLog::info(' ========= Script Started ========= ');

myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;
$sphinx = new kSphinxSearchManager();

foreach ($entryVendorTaskList as $entryVendorTaskId)
{
	$entryVendorTaskId = trim($entryVendorTaskId);

	EntryVendorTaskPeer::setUseCriteriaFilter(false);
	$entryVendorTask = EntryVendorTaskPeer::retrieveByPK($entryVendorTaskId);

	if (!$dryRun)
	{
		$sphinx->saveToSphinx($entryVendorTask, false, false);
	}
	else
	{
		print $sphinx->getSphinxSaveSql($entryVendorTask, false, false) . ';' . PHP_EOL;
	}
}

if (!$dryRun)
{
	kMemoryManager::clearMemory();
}

KalturaLog::info(' ========= Script Finished ========= ');
?>

