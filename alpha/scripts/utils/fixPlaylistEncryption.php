<?php
ini_set("memory_limit","1024M");
if (count($argv) < 3)
{
	echo "php $argv[0] {partnerId} {logFilePath} <fixCorrupted>.\n";
	echo "for example: php /opt/kaltura/app/alpha/scripts/utils/fixPlaylistEncryption.php 2301 /tmp/playlistLog.txt true\n";
	die ('Missing arguments.\n');
}

require_once(__DIR__ . '/../bootstrap.php');
require_once(dirname(__FILE__).'/../bootstrap.php');

$partnerId = $argv[1];
$logFilePath = $argv[2];
if (count($argv) > 3)
{
	$fixCorrupted = $argv[3];
}
else
{
	$fixCorrupted = "false";
}

if (!PartnerPeer::retrieveByPK($partnerId))
{
	die ('Partner ID not found.');
}

$logFile = fopen("$logFilePath", "a") or die("Unable to open file!");
fwrite($logFile, "Running fix playlistEncryption script for partner {$partnerId} with fixCorrupted set to {$fixCorrupted}\n");
$c = new Criteria();
$c->add(entryPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
$c->add(entryPeer::STATUS, entryStatus::READY, Criteria::EQUAL);
$c->add(entryPeer::TYPE, entryType::PLAYLIST, Criteria::EQUAL);
BaseentryPeer::setUseCriteriaFilter(false);
$playlists = entryPeer::doSelect($c);
$count = 0;
try
{
	foreach ($playlists as $playlist)
	{
		if($playlist->getUpdateAtAsInt() != $playlist->getCreatedAtAsInt())
		{
			$sync_key = $playlist->getSyncKey(kEntryFileSyncSubType::DATA, null);
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
					fwrite($logFile, implode(";", $logData) . "\n");
					$count++;
					if($fixCorrupted == "true")
					{
						fwrite($logFile,"updating content for ". $playlist->getEntryId(). "\n");
						$playlist->setDataContent($file_content);
						$playlist->save();
					}
				}
			}
		}
	}

	fwrite($logFile,"Found " . $count ." corrupted playlists\n");
}
catch(Exception $e)
{
	fclose($logFile);
}

fclose($logFile);