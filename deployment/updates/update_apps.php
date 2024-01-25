<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "bootstrap.php");

ini_set("memory_limit", "512M");
error_reporting(E_ALL);

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "ZendFramework", "*"));
KAutoloader::register();

const EXEC_STATUS_FAILURE = 1;
const EXEC_STATUS_SUCCESS = 0;

$appUpdater = new AppUpdater();
$appUpdater->init();
$appUpdater->run();
exit(EXEC_STATUS_SUCCESS);

class AppUpdater
{
	private $statsBoyInternalDomain;
	private $currentAppsVersionsMap = array();
	private $nextAppsVersionsMap = array();
	private $appVersionsDiff = array();
	
	public function init()
	{
		$this->statsBoyInternalDomain = $appsHost = kConf::get('apps_host', kConfMapNames::RUNTIME_CONFIG, null);
		$this->buildCurrentAppVersions();;
		$this->buildNextAppVersions();;
	}
	
	public function run()
	{
		$this->buildAppsVersionDiffMap();
		$this->deployApps();
	}
	
	private function buildCurrentAppVersions()
	{
		$versionMapUrl = $this->statsBoyInternalDomain . "/app_versions";
		
		$curlWrapper = new KCurlWrapper();
		$response = $curlWrapper->exec($versionMapUrl);
		if (!$response || $curlWrapper->getHttpCode() !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err("Failed to get version map when calling [$versionMapUrl], Error code : {$curlWrapper->getHttpCode()}, Error: {$curlWrapper->getError()}");
			die(EXEC_STATUS_FAILURE);
		}
		
		preg_match_all('/app="(.*)",version="(.*)"/m', $response, $appVersionMatches);
		
		$length = count($appVersionMatches[1]);
		for ($i = 0; $i < $length; $i++)
		{
			$this->currentAppsVersionsMap[$appVersionMatches[1][$i]] = $appVersionMatches[2][$i];
		}
	}
	
	private function buildNextAppVersions()
	{
		$this->nextAppsVersionsMap = kConf::getMap('kaltura_app_versions');
	}
	
	private function buildAppsVersionDiffMap()
	{
		foreach ($this->nextAppsVersionsMap as $appName => $appVersion) {
			if (!isset($this->currentAppsVersionsMap[$appName]) || $this->currentAppsVersionsMap[$appName] != $appVersion)
			{
				$this->appVersionsDiff[$appName] = $appVersion;
			}
		}
	}
	
	private function deployApps()
	{
		foreach ($this->appVersionsDiff as $appName => $appVersion)
		{
			$this->deployApp($appName, $appVersion);
		}
	}
	
	private function deployApp($appName, $appVersion)
	{
		$tmpConfigFileDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $appName;
		if (!mkdir($tmpConfigFileDir) &&
			mkdir("$tmpConfigFileDir/v2/") &&
			mkdir("$tmpConfigFileDir/v7/"))
		{
			KalturaLog::err("Failed to create tmp dir for app config download [$tmpConfigFileDir]");
			die(EXEC_STATUS_FAILURE);
		}
		
		if (!$this->deploy($tmpConfigFileDir))
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
				$baseUrl = $this->statsBoyInternalDomain . "apps/kmc-ng/" . $appVersion;
				$this->deployV2Player($tmpDir, $baseUrl, $appName, $appVersion);
				$this->deployV7Player($tmpDir, $baseUrl, $appName, $appVersion);
				break;
			case 'kea':
				$baseUrl = $this->statsBoyInternalDomain . "apps/kea/" . $appVersion;
				$this->deployV2Player($tmpDir, $baseUrl, $appName, $appVersion);
				$this->deployV7Player($tmpDir, $baseUrl, $appName, $appVersion);
				break;
			case 'captions':
				$baseUrl = $this->statsBoyInternalDomain . "apps/captionstudio/" . $appVersion;
				$this->deployV7Player($tmpDir, $baseUrl, $appName, $appVersion);
				break;
			case 'kmcanalytics':
				$baseUrl = $this->statsBoyInternalDomain . "apps/kmcAnalytics/" . $appVersion;
				$this->deployV2Player($tmpDir, $baseUrl, $appName, $appVersion);
				$this->deployV7Player($tmpDir, $baseUrl, $appName, $appVersion);
				break;
			default:
				$baseUrl = $this->statsBoyInternalDomain . "apps/$appName/" . $appVersion;
				$this->deployV7Player($tmpDir, $baseUrl, $appName, $appVersion);
				break;
		}
	}
	
	private function deployV2Player($tmpDir, $baseUrl, $appName, $appVersion)
	{
		$curlWrapper = new KCurlWrapper();
		$downloadConfig = $curlWrapper->exec($baseUrl . "/deploy/config.ini", "$tmpDir/v2/");
		if (!$downloadConfig || $curlWrapper->getHttpCode() !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err("Failed to download v2 player config from [$baseUrl/deploy/config.ini], Error code : {$curlWrapper->getHttpCode()}, Error: {$curlWrapper->getError()}");
			die(EXEC_STATUS_FAILURE);
		}
		exec("sed -i 's/component.version=.*/component.version=latest/g' $baseUrl/deploy/config.ini");
		
		$curlWrapper = new KCurlWrapper();
		$downloadPlayerJson = $curlWrapper->exec($baseUrl . "/deploy/player.json", "$tmpDir/v2/");
		if (!$downloadPlayerJson || $curlWrapper->getHttpCode() !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err("Failed to download v2 player config from [$baseUrl/deploy/player.json], Error code : {$curlWrapper->getHttpCode()}, Error: {$curlWrapper->getError()}");
			die(EXEC_STATUS_FAILURE);
		}
		
		$curlWrapper = new KCurlWrapper();
		$downloadPlayerXml = $curlWrapper->exec($baseUrl . "/deploy/player.xml", "$tmpDir/v2/");
		if (!$downloadPlayerXml || $curlWrapper->getHttpCode() !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err("Failed to download v2 player config from [$baseUrl/deploy/player.xml], Error code : {$curlWrapper->getHttpCode()}, Error: {$curlWrapper->getError()}");
			die(EXEC_STATUS_FAILURE);
		}
		
		return $this->runDeployV2("$tmpDir/v2/");
		
	}
	
	private function deployV7Player()
	{
		$curlWrapper = new KCurlWrapper();
		$downloadConfig = $curlWrapper->exec($baseUrl . "/deploy/config.ini", "$tmpDir/v7/");
		if (!$downloadConfig || $curlWrapper->getHttpCode() !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err("Failed to download v2 player config from [$baseUrl/deploy/config.ini], Error code : {$curlWrapper->getHttpCode()}, Error: {$curlWrapper->getError()}");
			die(EXEC_STATUS_FAILURE);
		}
		exec("sed -i 's/component.version=.*/component.version=latest/g' $baseUrl/deploy/config.ini");
		
		$downloadPlayerJson = $curlWrapper = new KCurlWrapper();
		$curlWrapper->exec($baseUrl . "/deploy/player.json", "$tmpDir/v7/");
		if (!$downloadPlayerJson || $curlWrapper->getHttpCode() !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err("Failed to download v2 player config from [$baseUrl/deploy/player.json], Error code : {$curlWrapper->getHttpCode()}, Error: {$curlWrapper->getError()}");
			die(EXEC_STATUS_FAILURE);
		}
		
		return $this->runDeployV2("$tmpDir/v7/");
	}
	
	private function runDeployV2($configDirLocation)
	{
		//php /opt/kaltura/app/deployment/uiconf/deploy_v2.php --user=www-data --group=www-data --ini=./deploy/config.ini
		$command = "php /opt/kaltura/app/deployment/uiconf/deploy_v2.php --user=www-data --group=www-data --ini=$configDirLocation/config.ini";
		$res = exec($command, $output, $return_var);
		if($return_var != 0)
		{
			KalturaLog::err("ERROR: failed to run $command");
			die(EXEC_STATUS_FAILURE);
		}
	}
}