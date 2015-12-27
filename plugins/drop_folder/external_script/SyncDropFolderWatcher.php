<?php
//script call:
// php SyncDropFolderWatcher.php action folder_path file_name file_size >> /var/log/SyncDropFolderWatcher.log
//example:
// php SyncDropFolderWatcher.php 1 /web/content/drop_folder1/file1.flv 0 >> /var/log/SyncDropFolderWatcher.log
// php SyncDropFolderWatcher.php 2 /web/content/drop_folder1/file1.flv 1595 >> /var/log/SyncDropFolderWatcher.log

require_once(dirname(__file__).'/lib/KalturaClient.php');
require_once(dirname(__file__).'/lib/KalturaPlugins/KalturaDropFolderClientPlugin.php');

class SyncDropFolderWatcherLogger implements IKalturaLogger
{
	private $prefix = '';
	
	function __construct($logPrefix)
	{
		$this->prefix = $logPrefix;
	}
	
	function log($msg)
	{
		writeLog($this->prefix, $msg);
	}
}

const DETECTED = 1;
const UPLOADED = 2;
const RENAMED = 3;

$logPrefix = rand();

if($argc < 3)
{
	writeLog($logPrefix, 'Wrong number of arguments');
	return;
}
$action = $argv[1];
$filePath = $argv[2];
$fileSize = $argv[3];

$config = parse_ini_file("config.ini");
$serviceUrl = $config['service_url'];
writeLog($logPrefix, 'Service URL '.$serviceUrl);
$sleepSec = $config['sleep_time']; 

$fileName=basename($filePath);
$folderPath = dirname($filePath);


writeLog($logPrefix, '---------------------------- Start handling --------------------------');
writeLog($logPrefix, 'action:'.$action);
writeLog($logPrefix, 'file path:'.$filePath);
writeLog($logPrefix, 'folder path:'.$folderPath);
writeLog($logPrefix, 'file name:'.$fileName);
writeLog($logPrefix, 'file size:'.$fileSize);


$kClientConfig = new KalturaConfiguration();
$kClientConfig->serviceUrl = $serviceUrl;
$kClientConfig->curlTimeout = 180;
$kClientConfig->setLogger(new SyncDropFolderWatcherLogger($logPrefix));

$kClient = new KalturaClient($kClientConfig);
$kClient->setPartnerId(-1);
$dropFolderPlugin = KalturaDropFolderClientPlugin::get($kClient);

try 
{
	$folder = null;
	$filter = new KalturaDropFolderFilter();
	$filter->pathEqual = $folderPath;
	$filter->typeEqual = KalturaDropFolderType::LOCAL;
	$filter->statusIn = KalturaDropFolderStatus::ENABLED. ','. KalturaDropFolderStatus::ERROR;
	$dropFolders = $dropFolderPlugin->dropFolder->listAction($filter);	
	writeLog($logPrefix, 'found '.$dropFolders->totalCount.' folders');
	if($dropFolders->totalCount == 1)
	{
		$folder = $dropFolders->objects[0];
		writeLog($logPrefix, 'drop folder id '.$folder->id);
		
		$ignorePatterns = array_map('trim', explode(',', $folder->ignoreFileNamePatterns));
		foreach ($ignorePatterns as $ignorePattern)
		{
			if (!is_null($ignorePattern) && ($ignorePattern != '') && fnmatch($ignorePattern, $fileName)) 
			{
				writeLog($logPrefix, 'Ignoring file matching ignore pattern ['.$ignorePattern.']');
				return;
			}
		}
		
		//impersonate
		$kClientConfig->partnerId = $folder->partnerId;
		$kClient->setConfig($kClientConfig);
		
		if($action == DETECTED)
		{
			writeLog($logPrefix, 'Handle file detected');
			$file = addFile($folder->id, $filePath, $fileSize, $dropFolderPlugin);			
			writeLog($logPrefix, 'created file with id '.$file->id);
			
		}
		else if($action == UPLOADED)
		{
			$file = getFile($folder->id, $fileName, $dropFolderPlugin);
			$now = time();
			// In some cases we better sleep before handling the file due to NFS latency
			// 1. the file doens't exist on the disk - maybe it was a temporary file name (some ftp clients may upload a tmp file and then rename it)
			// 2. the file wasn't picked up when the upload started
			// 3. the file started to upload less than $sleepSec seconds ago and may not be synced on another nfs node
			if (!file_exists($filePath) || !$file || $file->uploadStartDetectedAt > $now - $sleepSec)
			{
				// sleep at most sleepSec from start of file upload time
				$maxSleepTime = $file ? ($file->uploadStartDetectedAt + $sleepSec - $now) : $sleepSec;
				// restrict sleep time to ignore any date time anomalies
				$maxSleepTime = max(0, min($maxSleepTime, $sleepSec));
				writeLog($logPrefix, 'Sleeping for '.$maxSleepTime.' seconds ...');
				sleep($maxSleepTime);
				$file = getFile($folder->id, $fileName, $dropFolderPlugin);
			}
			
			writeLog($logPrefix, 'Handle file uploaded');
				
			writeLog($logPrefix, 'Check if file exists on the file system...');
			
			clearstatcache();
			$fileExists = file_exists($filePath);
			if($fileExists)
				writeLog($logPrefix, 'file exists on the file system');
			else 
				writeLog($logPrefix, 'file does not exists on the file system');

			if ($file && ($file->status == KalturaDropFolderFileStatus::PARSED || $file->status == KalturaDropFolderFileStatus::UPLOADING))
			{
				writeLog($logPrefix, 'found drop folder file in status PARSED or UPLOADING with id '.$file->id);
				if ($fileExists) //file exists on the file system and in database
				{
					updateFile($file->id, $fileSize, $filePath, $dropFolderPlugin);				
					writeLog($logPrefix, 'drop folder file id '.$file->id.' updated ');
				}
				else //file does not exists on file system (temporary file), but exists in database
				{
					$dropFolderPlugin->dropFolderFile->updateStatus($file->id, KalturaDropFolderFileStatus::PURGED);
					writeLog($logPrefix, 'file deleted from the file system, status updated to PURGED');
				}
			}
			else if ($fileExists)
			{
				writeLog($logPrefix, 'No drop folder file exists with status UPLOADING or PARSED');
				if ($file && $file->fileSize == $fileSize && $file->lastModificationTime == filemtime($filePath)) //an older drop folder file already exists
				{
					writeLog($logPrefix, 'This is a duplicated UPLOADED event, ignoring it due to drop folder file id ' . $file->id);
				}
				else //file exists on the file system, but not in database 
				{
					$file = addPendingFile($folder->id, $filePath, $fileSize, $dropFolderPlugin);
					writeLog($logPrefix, 'created PENDING file with id '.$file->id);
				}
			}
		}
		else if($action == RENAMED)
		{
			writeLog($logPrefix, 'Handle file renamed');
			$file = addPendingFile($folder->id, $filePath, $fileSize, $dropFolderPlugin);			
			writeLog($logPrefix, 'created PENDING file with id '.$file->id);
			
		}		
		else 
		{
			writeLog($logPrefix, 'Error - invalid action');
		}
	}
	else
	{
		writeLog($logPrefix, 'Error - folder does not exists');
	}
}
catch (Exception $e)
{
	writeLog($logPrefix, 'Exception '.$e->getMessage());
	writeLog($logPrefix, $e->getTraceAsString());
}

//unimpersonate
$kClientConfig->partnerId = -1;
$kClient->setConfig($kClientConfig);

writeLog($logPrefix, '---------------------------- Finish handling --------------------------');

function writeLog($prefix, $message)
{
	echo $prefix.':'.$message."\n";
}

function addFile($folderId, $filePath, $fileSize, $dropFolderPlugin)
{
	$newDropFolderFile = new KalturaDropFolderFile();
	$newDropFolderFile->dropFolderId = $folderId;
	$newDropFolderFile->fileName = basename($filePath);
	$newDropFolderFile->fileSize = $fileSize;
	$newDropFolderFile->lastModificationTime = filemtime($filePath);
	$newDropFolderFile->uploadStartDetectedAt = time();
			
	$file = $dropFolderPlugin->dropFolderFile->add($newDropFolderFile);
	return $file;
}

function updateFile($fileId, $fileSize, $filePath, $dropFolderPlugin)
{
	$updateDropFolderFile = new KalturaDropFolderFile();				
	$updateDropFolderFile->lastModificationTime = filemtime($filePath);
	$updateDropFolderFile->uploadEndDetectedAt = time();
	$updateDropFolderFile->fileSize = $fileSize;
	$dropFolderPlugin->dropFolderFile->update($fileId, $updateDropFolderFile);
	$dropFolderPlugin->dropFolderFile->updateStatus($fileId, KalturaDropFolderFileStatus::PENDING);	
}

function addPendingFile($folderId, $filePath, $fileSize, $dropFolderPlugin)
{
	$file = addFile($folderId, $filePath, $fileSize, $dropFolderPlugin);
	updateFile($file->id, $fileSize, $filePath, $dropFolderPlugin);
	return $file;
}

function getFile($folderId, $fileName, $dropFolderPlugin)
{
	$filter = new KalturaDropFolderFileFilter();
	$filter->dropFolderIdEqual = $folderId;
	$filter->fileNameEqual = $fileName;
	$dropFolderFiles = $dropFolderPlugin->dropFolderFile->listAction($filter);
	if($dropFolderFiles->totalCount == 1)
		return $dropFolderFiles->objects[0];
	else 
		return null;	
}
