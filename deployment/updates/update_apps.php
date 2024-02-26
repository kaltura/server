<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "bootstrap.php");

ini_set("memory_limit", "512M");
error_reporting(E_ALL);

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "ZendFramework", "*"));
KAutoloader::register();

const EXEC_STATUS_FAILURE = 1;
const EXEC_STATUS_SUCCESS = 0;

$options = getopt('a:v:', array(
	'application:',
	'version:',
));

$application = null;
$version = null;

if(isset($options['a']))
{
	$application = $options['a'];
}
if (isset($options['application']))
{
	$application = $options['application'];
}

if(isset($options['v']))
{
	$version = $options['v'];
}
if (isset($options['version']))
{
	$version = $options['version'];
}

if(!$application || !$version)
{
	KalturaLog::debug("Missing mandatory param [$application] [$version]");
	exit(EXEC_STATUS_FAILURE);
}

$appUpdater = new AppUpdater();
$appUpdater->init($application, $version);
$appUpdater->run();
exit(EXEC_STATUS_SUCCESS);

class AppUpdater
{
	private $statsBoyInternalDomain;
	
	private $application;
	
	private $version;
	
	public function init($application, $version)
	{
		$this->statsBoyInternalDomain = kConf::get('apps_host', kConfMapNames::RUNTIME_CONFIG, null);
		if(!$this->statsBoyInternalDomain)
		{
			KalturaLog::debug("Failed to get stats boy internal url [{$this->statsBoyInternalDomain}], application will not be deployed");
			exit(EXEC_STATUS_FAILURE);
		}
		
		$this->application = $application;
		$this->version = $version;
	}
	
	public function run()
	{
		$this->deployApp($this->application, $this->version);
	}
	
	private function deployApp($appName, $appVersion)
	{
		$tmpConfigFileDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $appName;
		kFile::fullMkfileDir("$tmpConfigFileDir/v2/");
		kFile::fullMkfileDir("$tmpConfigFileDir/v7/");
		
		if (!kFile::checkFileExists("$tmpConfigFileDir/v2/") ||
			!kFile::checkFileExists("$tmpConfigFileDir/v7/"))
		{
			KalturaLog::err("Failed to create tmp dir for app config download [$tmpConfigFileDir]");
			die(EXEC_STATUS_FAILURE);
		}
		
		if (!$this->deploy($tmpConfigFileDir, $appName, $appVersion))
		{
			KalturaLog::err("Failed to download config file [$tmpConfigFileDir]");
			die(EXEC_STATUS_FAILURE);
		}
		
	}
	
	private function deploy($tmpDir, $appName, $appVersion)
	{
		switch ($appName)
		{
			case 'kmcng':
				$baseUrl = $this->statsBoyInternalDomain . "/apps/kmcng/" . $appVersion;
				$this->deployV2Player($tmpDir, $baseUrl, $appName, $appVersion);
				$this->deployV7Player($tmpDir, $baseUrl, $appName, $appVersion);
				break;
			case 'kea':
				$baseUrl = $this->statsBoyInternalDomain . "/apps/kea/" . $appVersion;
				$this->deployV2Player($tmpDir, $baseUrl, $appName, $appVersion);
				$this->deployV7Player($tmpDir, $baseUrl, $appName, $appVersion);
				break;
			case 'captions':
				$baseUrl = $this->statsBoyInternalDomain . "/apps/captionstudio/" . $appVersion;
				$this->deployV7Player($tmpDir, $baseUrl, $appName, $appVersion);
				break;
			case 'kmcanalytics':
				$baseUrl = $this->statsBoyInternalDomain . "/apps/kmc-analytics/" . $appVersion;
				$this->deployV2Player($tmpDir, $baseUrl, $appName, $appVersion);
				$this->deployV7Player($tmpDir, $baseUrl, $appName, $appVersion);
				break;
			default:
				$baseUrl = $this->statsBoyInternalDomain . "/apps/$appName/" . $appVersion;
				$this->deployV7Player($tmpDir, $baseUrl, $appName, $appVersion);
				break;
		}
		
		return true;
	}
	
	private function deployV2Player($tmpDir, $baseUrl, $appName, $appVersion)
	{
		$curlWrapper = new KCurlWrapper();
		$downloadConfig = $curlWrapper->exec($baseUrl . "/deploy/config.ini", "$tmpDir/v2/config.ini");
		if (!$downloadConfig || $curlWrapper->getHttpCode() !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err("Failed to download v2 player config from [$baseUrl/deploy/config.ini], Error code : {$curlWrapper->getHttpCode()}, Error: {$curlWrapper->getError()}");
			die(EXEC_STATUS_FAILURE);
		}
		
		$curlWrapper = new KCurlWrapper();
		$downloadPlayerJson = $curlWrapper->exec($baseUrl . "/deploy/player.json", "$tmpDir/v2/player.json");
		if (!$downloadPlayerJson || $curlWrapper->getHttpCode() !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err("Failed to download v2 player config from [$baseUrl/deploy/player.json], Error code : {$curlWrapper->getHttpCode()}, Error: {$curlWrapper->getError()}");
			die(EXEC_STATUS_FAILURE);
		}
		
		$curlWrapper = new KCurlWrapper();
		$downloadPlayerXml = $curlWrapper->exec($baseUrl . "/deploy/player.xml", "$tmpDir/v2/player.xml");
		if (!$downloadPlayerXml || $curlWrapper->getHttpCode() !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err("Failed to download v2 player config from [$baseUrl/deploy/player.xml], Error code : {$curlWrapper->getHttpCode()}, Error: {$curlWrapper->getError()}");
			die(EXEC_STATUS_FAILURE);
		}
		
		return $this->runDeployV2("$tmpDir/v2/");
		
	}
	
	private function deployV7Player($tmpDir, $baseUrl, $appName, $appVersion)
	{
		$curlWrapper = new KCurlWrapper();
		$downloadConfig = $curlWrapper->exec($baseUrl . "/deploy_v7/config.ini", "$tmpDir/v7/config.ini");
		if (!$downloadConfig || $curlWrapper->getHttpCode() !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err("Failed to download v7 player config from [$baseUrl/deploy/config.ini], Error code : {$curlWrapper->getHttpCode()}, Error: {$curlWrapper->getError()}");
			die(EXEC_STATUS_FAILURE);
		}
		
		$downloadPlayerJson = $curlWrapper = new KCurlWrapper();
		$curlWrapper->exec($baseUrl . "/deploy_v7/player.json", "$tmpDir/v7/player.json");
		if (!$downloadPlayerJson || $curlWrapper->getHttpCode() !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err("Failed to download v7 player config from [$baseUrl/deploy/player.json], Error code : {$curlWrapper->getHttpCode()}, Error: {$curlWrapper->getError()}");
			die(EXEC_STATUS_FAILURE);
		}
		
		return $this->runDeployV2("$tmpDir/v7/");
	}
	
	private function runDeployV2($configDirLocation)
	{
		$command = "php /opt/kaltura/app/deployment/uiconf/deploy_v2.php --user=www-data --group=www-data --ini=$configDirLocation/config.ini";
		KalturaLog::debug("Running command [$command]");
		$res = exec($command, $output, $return_var);
		if($return_var != 0)
		{
			KalturaLog::err("ERROR: failed to run $command");
			die(EXEC_STATUS_FAILURE);
		}
	}
}