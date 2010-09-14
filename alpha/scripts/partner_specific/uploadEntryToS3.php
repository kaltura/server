<?php
require_once(realpath(dirname(__FILE__)).'/../../config/sfrootdir.php');
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       true);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

$databaseManager = new sfDatabaseManager();
$databaseManager->initialize();

$entry_id = $argv[1];
$force = @$argv[2];

$entry = entryPeer::retrieveByPk($entry_id);
if (!$entry)
{
	echo "entry $entry_id not found\n";
	die;
}

if ($entry->getExtStorageUrl())
{
	if ($force)
	{
		$entry->setExtStorageUrl(null);
		$entry->justSave();
	}
	else
	{
		echo "entry $entry_id already stored at ".$entry->getExtStorageUrl()."\n";
		die;
	}
}

$file_name = "video.flv";

unlink($file_name);

exec("wget localhost/flvclipper/entry_id/$entry_id -O $file_name");

if (!file_exists($file_name) || !filesize($file_name))
{
	echo  "entry $entry_id failed to download\n";
	die;
}

$accessKey = '0DVHC7TVFR2VBS2HMG02';
$secretKey = 'uXThfXisk1p4ZhWX4S0t6byq8OkeY19Ti81ZIOIb';
$s3 = new S3($accessKey, $secretKey);

$path = trim($entry->getDataPath(), '/');

if (!myFlvStaticHandler::isFlv($file_name))
{
	$new_file_name = str_replace(".flv", ".mp4", $file_name);
	rename($file_name, $new_file_name);
	$file_name = $new_file_name;

	$path = str_replace(".flv", ".mp4", $path);
}

echo "upload $file_name size ".filesize($file_name)." to $path\n";

$res = $s3->putObjectFile($file_name, 's3kaltura', $path, S3::ACL_PUBLIC_READ);

if ($res)
{
	$info = $s3->getObjectInfo('s3kaltura', $path);
	if ($info && $info['size'] == filesize($file_name))
	{
		$ext_storage_url = "http://s3kaltura.s3.amazonaws.com/$path";

		$entry = entryPeer::retrieveByPk($entry_id);
		$entry->setExtStorageUrl($ext_storage_url);
		$entry->justSave();

		echo "entry $entry_id uploaded to: $ext_storage_url\n";
	}
	else
	{
		echo "error uploading entry $entry_id size ".filesize($file_name)." s3 info ".print_r($info, true);
	}
}

unlink($file_name);

