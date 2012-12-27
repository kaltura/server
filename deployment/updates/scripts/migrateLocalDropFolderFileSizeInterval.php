<?php
/**
 * Usage:
 * php migrateLocalDropFolderFileSizeinterval.php [realrun/dryrun]
 * 
 * Defaults are: dryrun
 * @package deploy
 * @subpackage update
 */

require_once(dirname(__FILE__).'/../../bootstrap.php');

$fileSizeInterval = 3600;

$dryRun = true;
if(isset($argv[1]) && strtolower($argv[1]) == 'realrun')
{
	$dryRun = false;
}
else 
{
	KalturaLog::info('Using dry run mode');
}
KalturaStatement::setDryRun($dryRun);
	
kEventsManager::enableDeferredEvents(false);

$criteria = new Criteria();
$criteria->add(DropFolderPeer::TYPE, DropFolderType::LOCAL, Criteria::EQUAL);
$criteria->add(DropFolderPeer::STATUS, array(DropFolderStatus::ENABLED, DropFolderStatus::DISABLED), Criteria::IN);

$folders = DropFolderPeer::doSelect($criteria);
$migrated = 0;
foreach ($folders as $folder) 
{
	/* @var $folder DropFolder */

	$folder->setFileSizeCheckInterval($fileSizeInterval);	
	if($folder->save())
	{
		$migrated++;
		KalturaLog::info("Migrated folder [" . $folder->getId() . "] ");
	}
}

KalturaLog::info("Done - migrated $migrated items");

