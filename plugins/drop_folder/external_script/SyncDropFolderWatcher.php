<?php
//script call:
// php SyncDropFolderWatcher.php action folder_path file_name file_size >> /var/log/SyncDropFolderWatcher.log
//example:
// php SyncDropFolderWatcher.php 1 /web/content/drop_folder1 file1.flv 0 >> /var/log/SyncDropFolderWatcher.log
// php SyncDropFolderWatcher.php 2 /web/content/drop_folder1 file1.flv 1595 >> /var/log/SyncDropFolderWatcher.log

const DETECTED = 1;
const UPLOADED = 2;

if($argc < 4)
{
	echo 'Wrong number of arguments';
	return;
}
$action = $argv[1];
$folderPath = $argv[2];
$fileName = $argv[3];
$fileSize = $argv[4];

echo '---------------------------- Start handling --------------------------'."\n";
echo 'action:'.$action."\n";
echo 'folder path:'.$folderPath."\n";
echo 'file name:'.$fileName."\n";
echo 'file size:'.$fileSize."\n";

require_once 'lib/KalturaClient.php';
require_once 'lib/KalturaPlugins/KalturaDropFolderClientPlugin.php';

$config = parse_ini_file("config.ini");
$serviceUrl = $config['serviceUrl'];
echo 'Service URL '.$serviceUrl."\n";

$kClientConfig = new KalturaConfiguration(-1);
$kClientConfig->serviceUrl = $serviceUrl;
$kClientConfig->curlTimeout = 180;

$kClient = new KalturaClient($kClientConfig);
$dropFolderPlugin = KalturaDropFolderClientPlugin::get($kClient);

try 
{
	$folder = null;
	$filter = new KalturaDropFolderFilter();
	$filter->pathEqual = $folderPath;
	$filter->typeEqual = KalturaDropFolderType::LOCAL;
	$dropFolders = $dropFolderPlugin->dropFolder->listAction($filter);	
	echo 'found '.$dropFolders->totalCount.' folders'."\n";
	if($dropFolders->totalCount == 1)
	{
		$folder = $dropFolders->objects[0];
		echo 'drop folder id '.$folder->id."\n";
		
		//impersonate
		$kClientConfig->partnerId = $folder->partnerId;
		$kClient->setConfig($kClientConfig);
		
		if($action == DETECTED)
		{
			echo 'Handle file detected'."\n";
			$detectedDropFolderFile = new KalturaDropFolderFile();
	    	$detectedDropFolderFile->dropFolderId = $folder->id;
	    	$detectedDropFolderFile->fileName = $fileName;
	    	$detectedDropFolderFile->fileSize = $fileSize;
	    	$detectedDropFolderFile->lastModificationTime = time(); 
	    	$detectedDropFolderFile->uploadStartDetectedAt = time();
			
			$file = $dropFolderPlugin->dropFolderFile->detected($detectedDropFolderFile);
			echo 'created file with id '.$file->id."\n";
		}
		else if($action == UPLOADED)
		{
			echo 'Handle file uploaded'."\n";
			$filter = new KalturaDropFolderFileFilter();
			$filter->dropFolderIdEqual = $folder->id;
			$filter->fileNameEqual = $fileName;
			$dropFolderFiles = $dropFolderPlugin->dropFolderFile->listAction($filter);
			echo 'found '.$dropFolderFiles->totalCount.' files'."\n";
			if($dropFolderFiles->totalCount == 1)
			{
				$file = $dropFolderFiles->objects[0];
				echo 'drop folder file id '.$file->id."\n";
				$updateDropFolderFile = new KalturaDropFolderFile();				
				$updateDropFolderFile->lastModificationTime = time();
				$updateDropFolderFile->uploadEndDetectedAt = time();
				$updateDropFolderFile->fileSize = $fileSize;
				$dropFolderPlugin->dropFolderFile->update($file->id, $updateDropFolderFile);
				$dropFolderPlugin->dropFolderFile->updateStatus($file->id, KalturaDropFolderFileStatus::PENDING);	
				echo 'file updated '."\n";					
			}
			else
			{
				echo 'Error - invalid file name'."\n";
			}
		}
		else 
		{
			echo 'Error - invalid action'."\n";
		}
	}
	else
	{
		echo 'Error - folder does not exist'."\n";
	}
}
catch (Exception $e)
{
	echo 'Exception '.$e->getMessage()."\n".$e->getTraceAsString()."\n";
}

//unimpersonate
$kClientConfig->partnerId = -1;
$kClient->setConfig($kClientConfig);

echo '---------------------------- Finish handling --------------------------'."\n";
