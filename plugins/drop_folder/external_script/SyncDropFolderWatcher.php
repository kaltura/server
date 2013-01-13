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


$kClientConfig = new KalturaConfiguration(-1);
$kClientConfig->serviceUrl = $serviceUrl;
$kClientConfig->curlTimeout = 180;
$kClientConfig->setLogger(new SyncDropFolderWatcherLogger($logPrefix));

$kClient = new KalturaClient($kClientConfig);
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
			$file = addFile($folder->id, $fileName, $fileSize, $dropFolderPlugin);			
			writeLog($logPrefix, 'created file with id '.$file->id);
			
		}
		else if($action == UPLOADED)
		{
			writeLog($logPrefix, 'Sleeping for '.$sleepSec.' seconds ...');
			sleep($sleepSec);
			writeLog($logPrefix, 'Handle file uploaded');
			
			$file = getFile($folder->id, $fileName, $dropFolderPlugin);
			writeLog($logPrefix, 'drop folder file id '.$file->id);		
					
			writeLog($logPrefix, 'Check if file exists on the file system...');
			$fileExists = file_exists($filePath);
			if($fileExists)
				writeLog($logPrefix, 'file exists on the file system');
			else 
				writeLog($logPrefix, 'file does not exists on the file system');
				
			if($fileExists && $file) //file exists on the file system and in database
			{
				updateFile($file->id, $fileSize, $dropFolderPlugin);				
				writeLog($logPrefix, 'file updated ');
			}
			else if ($fileExists && !$file) //file exisit on the file system, but not in database
			{
				writeLog($logPrefix, 'No file exists with status UPLOADING or PARSED, adding new file');
				addPendingFile($folder->id, $fileName, $fileSize, $dropFolderPlugin);
				writeLog($logPrefix, 'created file with id '.$file->id);
				writeLog($logPrefix, 'file status updated to PENDING');											
			}
			else if(!$fileExists && $file) //file does not exist on file system (temporary file), but exists in database
			{
				$dropFolderPlugin->dropFolderFile->updateStatus($file->id, KalturaDropFolderFileStatus::PURGED);
				writeLog($logPrefix, 'file deleted from the file system, status updated to PURGED');
			}				
		}
		else if($action == RENAMED)
		{
			writeLog($logPrefix, 'Handle file renamed');
			$file = addPendingFile($folder->id, $fileName, $fileSize, $dropFolderPlugin);			
			writeLog($logPrefix, 'created file with id '.$file->id);
			writeLog($logPrefix, 'file status updated to PENDING');	
		}		
		else 
		{
			writeLog($logPrefix, 'Error - invalid action');
		}
	}
	else
	{
		writeLog($logPrefix, 'Error - folder does not exist');
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

function addFile($folderId, $fileName, $fileSize, $dropFolderPlugin)
{
	$newDropFolderFile = new KalturaDropFolderFile();
	$newDropFolderFile->dropFolderId = $folderId;
	$newDropFolderFile->fileName = $fileName;
	$newDropFolderFile->fileSize = $fileSize;
	$newDropFolderFile->lastModificationTime = time(); 
	$newDropFolderFile->uploadStartDetectedAt = time();
			
	$file = $dropFolderPlugin->dropFolderFile->add($newDropFolderFile);
	return $file;
}

function updateFile($fileId, $fileSize, $dropFolderPlugin)
{
	$updateDropFolderFile = new KalturaDropFolderFile();				
	$updateDropFolderFile->lastModificationTime = time();
	$updateDropFolderFile->uploadEndDetectedAt = time();
	$updateDropFolderFile->fileSize = $fileSize;
	$dropFolderPlugin->dropFolderFile->update($fileId, $updateDropFolderFile);
	$dropFolderPlugin->dropFolderFile->updateStatus($fileId, KalturaDropFolderFileStatus::PENDING);	
}

function addPendingFile($folderId, $fileName, $fileSize, $dropFolderPlugin)
{
	$file = addFile($folderId, $fileName, $fileSize, $dropFolderPlugin);
	$dropFolderPlugin->dropFolderFile->updateStatus($file->id, KalturaDropFolderFileStatus::PENDING);
	return $file;
}

function getFile($folderId, $fileName, $dropFolderPlugin)
{
	$filter = new KalturaDropFolderFileFilter();
	$filter->dropFolderIdEqual = $folderId;
	$filter->fileNameEqual = $fileName;
	$filter->statusIn = KalturaDropFolderFileStatus::PARSED.','.KalturaDropFolderFileStatus::UPLOADING;
	$dropFolderFiles = $dropFolderPlugin->dropFolderFile->listAction($filter);
	if($dropFolderFiles->totalCount == 1)
		return $dropFolderFiles->objects[0];
	else 
		return null;	
}
