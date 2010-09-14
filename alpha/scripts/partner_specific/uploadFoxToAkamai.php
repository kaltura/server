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

define('DOWNLOAD_URL_BASE', 'http://fnckal.download.akamai.com/ugc/');
define('FTP_UPLOAD_SERVER', 'ftp://fnckal.upload.akamai.com');
define('FTP_UPLOAD_USERPWD', 'fnckal:AghterFETb9265!');
define('FTP_BASEDIR', '/91966/ugc/');
define('UPLOADED_PARTNER_ID', 205562);
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
	$c->addAnd(entryPeer::MODERATION_STATUS, array(entry::ENTRY_MODERATION_STATUS_APPROVED, entry::ENTRY_MODERATION_STATUS_AUTO_APPROVED), Criteria::IN);
	$c->addAnd(entryPeer::TYPE, entry::ENTRY_TYPE_MEDIACLIP);
	//$c->addAnd(entryPeer::MEDIA_TYPE, array (entry::ENTRY_MEDIA_TYPE_VIDEO, entry::ENTRY_MEDIA_TYPE_AUDIO, entry::ENTRY_MEDIA_IMAGE), Criteria::IN);
	$c->addAnd(entryPeer::INDEXED_CUSTOM_DATA_1,null,Criteria::ISNULL);
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
	$datePrefix = strftime("%Y%m%d");
	curl_create_ftp_directory($datePrefix, FTP_BASEDIR);

	$datePrefix .= "/";

	$entry = entryPeer::retrieveByPk($entry_id);
	if (!$entry)
	{
		echo "entry $entry_id not found\n";
		return;
	}
	
	if ($entry->getIndexedCustomData1())
	{
		if ($force)
		{
			$entry->setIndexedCustomData1(null);
			$entry->justSave();
		}
		else
		{
			echo "entry $entry_id already stored\n";
			return;
		}
	}

	$result = true;

	if ($entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_VIDEO || $entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_AUDIO)
	{
		$c = new Criteria;
		$c->add(flavorAssetPeer::ENTRY_ID, $entry_id);
		$c->add(flavorAssetPeer::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_READY);
		$flavors = flavorAssetPeer::doSelect($c);
		if(count($flavors) && is_array($flavors))
		{
			foreach($flavors as $flavor)
			{
				$syncKey = $flavor->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
				if (kFileSyncUtils::file_exists($syncKey, false))
				{
					$target = $path = kFileSyncUtils::getReadyLocalFilePathForKey($syncKey);
					if ($path)
					{
						if (!myFlvStaticHandler::isFlv($path))
						{
							$target = str_replace(".flv", ".mp4", $target);
						}
						$result &= handleSingleFile($path, $datePrefix.pathinfo($target, PATHINFO_BASENAME));
					}
				}
			}
		}
	}
	else
	{
		$syncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
		if (kFileSyncUtils::file_exists($syncKey, false))
		{
			$target = $path = kFileSyncUtils::getReadyLocalFilePathForKey($syncKey);
			$result &= handleSingleFile($path, $datePrefix.pathinfo($target, PATHINFO_BASENAME));
		}
	}

	if ($result)
	{
		$entry->setIndexedCustomData1(1);
		$entry->justSave();
	}
}

function handleSingleFile($path, $target)
{
	echo "upload $path size ".filesize($path)." to $target\n";
	
	
	/** FTP user/pass for akamai **/
	$akamai_user = 'fnckal';
	$akamai_pass = 'AghterFETb9265\\!';
	$akamai_ftp = FTP_UPLOAD_SERVER.FTP_BASEDIR;
	
	// upload file via ftp

	$command = "curl -s -u $akamai_user:$akamai_pass -T $path $akamai_ftp$target";
	//echo $command."\n";

	$uploadOutput = array();
	exec($command, $uploadOutput, $uploadError);
	
	$downloadUrl = DOWNLOAD_URL_BASE.$target;
	
	if (!$uploadError)
	{
		 // do validation
		$command = "curl -s -u $akamai_user:$akamai_pass -I $akamai_ftp$target";
		exec($command, $headersArr, $uploadError);
		if($uploadError)
			$headersArr = array();
/*
		$headers = array();
		$headers = kFile::downloadUrlToString($downloadUrl, 2);
		$headersArr = explode(PHP_EOL, $headers);
*/

		$size = 0;
		foreach($headersArr as $line)
		{
			if(substr_count($line, 'Content-Length'))
			{
				$parts = explode(' ', $line);
				$size = (int)$parts[1];
			}
		}
		if ($size == filesize($path))
		{
			echo "entry $path uploaded to: $target\n";
			return true;
		}
		else
		{
			echo "error uploading entry $path size ".filesize($path)." akamai headers ".print_r($headers, true);
		}
	}

	return false;
}

function curl_create_ftp_directory($name, $curr_dir = '') {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, FTP_UPLOAD_SERVER);
	curl_setopt($ch, CURLOPT_USERPWD, FTP_UPLOAD_USERPWD);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$cmd = array();
	if ($curr_dir != '')
		$cmd[] = 'CWD '.$curr_dir;
	$cmd[] = 'MKD '.$name;
	curl_setopt($ch, CURLOPT_POSTQUOTE, $cmd);
	curl_exec ($ch);
}
/*
function curl_put_file($path) {
	$ch = curl_init();
	$fp = fopen($this->basePath.$path, "r");
	curl_setopt($ch, CURLOPT_URL, FTP_UPLOAD_SERVER);
	curl_setopt($ch, CURLOPT_USERPWD, FTP_UPLOAD_USERPWD);
	curl_setopt($ch, CURLOPT_UPLOAD, 1);
	curl_setopt($ch, CURLOPT_INFILE, $fp);
	curl_setopt($ch, CURLOPT_INFILESIZE, filesize($this->basePath.$path));
	curl_exec ($ch);
	$error = curl_errno($ch);
	curl_close ($ch);
	if ($error != 0) $this->errors .= $path.PHP_EOL;
	return;
}
*/
