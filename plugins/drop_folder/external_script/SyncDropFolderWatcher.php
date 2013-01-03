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
$tempFileExtentions = $config['temp_file_extentions'];
writeLog($logPrefix, 'Temp file extentions '.$tempFileExtentions);

$fileName=basename($filePath);
$fileName = getDropFolderFileName($fileName, $tempFileExtentions);
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
			writeLog($logPrefix, 'Handle file uploaded');
			$filter = new KalturaDropFolderFileFilter();
			$filter->dropFolderIdEqual = $folder->id;
			$filter->fileNameEqual = $fileName;
			$filter->statusIn = KalturaDropFolderFileStatus::PARSED.','.KalturaDropFolderFileStatus::UPLOADING;
			$dropFolderFiles = $dropFolderPlugin->dropFolderFile->listAction($filter);
			writeLog($logPrefix, 'found '.$dropFolderFiles->totalCount.' files');
			if($dropFolderFiles->totalCount == 1)
			{
				$file = $dropFolderFiles->objects[0];
				writeLog($logPrefix, 'drop folder file id '.$file->id);
				updateFile($file->id, $fileSize, $dropFolderPlugin);				
				writeLog($logPrefix, 'file updated ');					
			}
			else
			{
				writeLog($logPrefix, 'No file exists with status UPLOADING or PARSED, adding new file');
				$file = addFile($folder->id, $fileName, $fileSize, $dropFolderPlugin);
				writeLog($logPrefix, 'created file with id '.$file->id);
				$dropFolderPlugin->dropFolderFile->updateStatus($file->id, KalturaDropFolderFileStatus::PENDING);	
				writeLog($logPrefix, 'file status updated to PENDING');			
			}
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

echo '---------------------------- Finish handling --------------------------'."\n";

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

function getDropFolderFileName($physicalFileName, $tempFileExtentions)
{
	if(!$tempFileExtentions)
		return $physicalFileName;
	$tempExtentionsArr = explode(',', $tempFileExtentions);
	$dropFolderFileName = $physicalFileName;
	foreach ($tempExtentionsArr as $extention) 
	{
		if(substr_compare($physicalFileName, $extention, -strlen($extention), strlen($extention)) === 0)
		{
			$dropFolderFileName = basename($dropFolderFileName, $extention);
			return $dropFolderFileName;
		}
	}
	return $dropFolderFileName;
}
