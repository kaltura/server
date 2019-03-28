<?php
ini_set("memory_limit","1024M");
require_once(__DIR__ . '/../bootstrap.php');
require_once(dirname(__FILE__).'/../bootstrap.php');
if (count($argv) < 4)
{
	echo "php $argv[0] {partnerId} {logFilePath} {onlyLog}.\n";
	die ('Missing arguments.\n');
}

$partnerId = $argv[1];
$logFilePath = $argv[2];
$onlyLog = $argv[3];

if (!PartnerPeer::retrieveByPK($partnerId))
{
	die ('Partner ID not found.');
}

$logFile = fopen("$logFilePath", "a") or die("Unable to open file!");
fwrite($logFile, "Running fix playlistEncryption script for partner {$partnerId} with onlyLog set to {$onlyLog}\n");
$c = new Criteria();
$c->add(entryPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
$c->add(entryPeer::STATUS, entryStatus::READY, Criteria::EQUAL);
$c->add(entryPeer::TYPE, entryType::PLAYLIST, Criteria::EQUAL);
BaseentryPeer::setUseCriteriaFilter(false);
$playlists = entryPeer::doSelect($c);
try
{
	foreach ($playlists as $playlist)
	{
		if($playlist->getUpdateAtAsInt() != $playlist->getCreatedAtAsInt())
		{
			$sync_key = $playlist->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA, null);
			list($file_sync, $local) = kFileSyncUtils::getReadyFileSyncForKey($sync_key, false, false);
			if($file_sync && $file_sync->isEncrypted() && !$file_sync->getFileSize())
			{
				$file_content = file_get_contents($file_sync->getFullPath());
				if(strpos($file_content, "1_") !== false || strpos($file_content, "0_") !== false)
				{
					$logData = array();
					$logData[] = $playlist->getEntryId();
					$logData[] = $file_sync->getVersion();
					$logData[] = $file_sync->getFullPath();
					$logData[] = $file_content;
					fwrite($logFile, "\n" . implode(";", $logData));
					if(!$onlyLog)
					{
						$playlist->setDataContent($file_content);
						$playlist->save();
					}
				}
			}
		}
	}
}
catch(Exception $e)
{
	fclose($logFile);
}

fclose($logFile);