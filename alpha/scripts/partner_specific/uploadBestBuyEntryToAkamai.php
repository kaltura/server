#!/usr/bin/php
<?php

define('CLASS_MAP_CACHE_FILE_PATH', '/tmp/findEntrySizesClassMap.cache');
define('ROOT_DIR', '/opt/kaltura/app');
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "infra", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "infra", "plugins", "*"));
KAutoloader::setClassMapFilePath(CLASS_MAP_CACHE_FILE_PATH);
KAutoloader::register();

DbManager::setConfig(kConf::getDB());
DbManager::initialize();

KalturaLog::setLogger(new KalturaStdoutLogger());
/*
require_once(realpath(dirname(__FILE__)).'/../../config/sfrootdir.php');
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       true);
*/
if(!@$argv[1])
{
	die("cron mode usage: \nphp ".$argv[0]." cron [force]\nsingle entry usage:\nphp ".$argv[0]." entry_id [force]\n");
}
set_time_limit(0); // to avoid having the script breaks in the middle of a long upload.

define('DOWNLOAD_URL_BASE', 'http://on.bestbuy.com/ns/');
define('SCP_UPLOAD_PATH', 'tagonline.upload.akamai.com:/95757/');
define('UPLOADED_PARTNER_ID', 37945);

/*
require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

$databaseManager = new sfDatabaseManager();
$databaseManager->initialize();
*/

$force = @$argv[2];
$entries = array();
if(isset($argv[1]) && $argv[1] == 'cron')
{
	$c = new Criteria;
	$c->add(entryPeer::PARTNER_ID, UPLOADED_PARTNER_ID);
	$c->addAnd(entryPeer::STATUS, entry::ENTRY_STATUS_READY);
	$c->addAnd(entryPeer::TYPE, entry::ENTRY_TYPE_MEDIACLIP);
	$c->addAnd(entryPeer::MEDIA_TYPE, array (entry::ENTRY_MEDIA_TYPE_VIDEO, entry::ENTRY_MEDIA_TYPE_AUDIO), Criteria::IN);
	$c->addAnd(entryPeer::CUSTOM_DATA, '%'.DOWNLOAD_URL_BASE.'%', Criteria::NOT_LIKE );
	$entryList = entryPeer::doSelect($c);
	if(count($entryList) && is_array($entryList))
	{
		foreach($entryList as $entry)
		{
			$entries[] = $entry->getId();
		}
		if(count($entries) != count($entryList))
		{
			echo 'something is wrong !';
			die;
		}
	}
	else
	{
		echo 'running in Cron mode, no entries found';
		die;
	}
}
else
{
	$entry_id = $argv[1];
	$entries[] = $entry_id;
}

foreach($entries as $entry_id)
{
	handleSingleEntry($entry_id, $force);
}

unlink(CLASS_MAP_CACHE_FILE_PATH);

function handleSingleEntry($entry_id, $force)
{
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
	
	$file_name = $entry_id.'.flv';
	
	unlink($file_name);
	
	exec("wget pa-apache1/flvclipper/entry_id/$entry_id -O $file_name");

	if (!file_exists($file_name) || !filesize($file_name))
	{
		echo  "entry $entry_id failed to download\n";
		die;
	}
	else
	{
		kLog::log(__METHOD__." - $file_name file exists");
	}	
	
	$path = trim($entry->getDataPath(), '/');
	
	if (!myFlvStaticHandler::isFlv($file_name))
	{
		$new_file_name = str_replace(".flv", ".mp4", $file_name);
		rename($file_name, $new_file_name);
		$file_name = $new_file_name;
	
		$path = str_replace(".flv", ".mp4", $path);
	}
	
	echo "upload $file_name size ".filesize($file_name)." to $path\n";
	
	
	//$akamai_user = 'bestbuyTest';
	//$akamai_pass = 'onetwo3hill';
	/** SCP user/pass for akamai **/
	$akamai_user = 'sshacs';
	$akamai_rsa_key_path = '/opt/kaltura/data/bestbuy/id_rsa_tagonline';
	
	// do upload of file $file_name to kalturans.upload.akamai.com
	$target_path = SCP_UPLOAD_PATH;
	
	$command = 'scp -i '.$akamai_rsa_key_path.' '.$file_name.' '.$akamai_user.'@'.$target_path;
	$uploadOutput = array();
	exec($command, $uploadOutput, $uploadError);
	
	$downloadUrl = DOWNLOAD_URL_BASE.$file_name;
	
	if (!$uploadError)
	{
		 // do validation
		$headers = kFile::downloadUrlToString($downloadUrl, 2);
		$headersArr = explode(PHP_EOL, $headers);
		$size = 0;
		foreach($headersArr as $line)
		{
			if(substr_count($line, 'Content-Length'))
			{
				$parts = explode(' ', $line);
				$size = (int)$parts[1];
			}
		}
		if ($size == filesize($file_name))
		{
			$ext_storage_url = $downloadUrl;
	
			$entry = entryPeer::retrieveByPk($entry_id);
			$entry->setExtStorageUrl($ext_storage_url);
			$entry->justSave();
	
			echo "entry $entry_id uploaded to: $ext_storage_url\n";
		}
		else
		{
			echo "error uploading entry $entry_id size ".filesize($file_name)." akamai headers ".print_r($headers, true);
		}
	}
	
	unlink($file_name);
}

