<?php

/**
 * @package Core
 * @subpackage externalWidgets
 */
class embedPlaykitJsAction extends sfAction
{

	const UI_CONF_ID_PARAM_NAME = "uiconf_id";
	const PARTNER_ID_PARAM_NAME = "partner_id";

	private $eTagHash = null;
	private $uiconfId = null;
	private $partnerId = null;
	private $bundleWebDirPath = null;
	private $bundleBuilderPath = null;
	private $sourcesPath = null;
	private $bundleConfig = null;
	private $bundlePath = null;
	private $regenerate = false;

	public function execute()
	{
		$this->initMembers();

		kLock::runLocked($this->bundle_name, array("embedPlaykitJsAction", "buildBundelLocked"), array($this));

		$bundleContent = $this->getbundleContent($this->bundlePath);

		// send cache headers
		$this->sendHeaders($bundleContent);

		echo($bundleContent);

		KExternalErrors::dieGracefully();
	}

	public static function buildBundelLocked($context)
	{
		//if bundle not exists or explicitly should be regenerated build it
		if (!file_exists($context->bundlePath) || $regenerate)
		{
			//build bundle and save in web dir
			$config = str_replace("\"", "'", json_encode($context->bundleConfig));
			if($config)
			{
				$command = $context->bundleBuilderPath . ' --name ' . $context->bundle_name . ' --config "' . base64_encode($config) . '" --dest ' . base64_encode($context->bundleWebDirPath) . " --source " . base64_encode($context->sourcesPath) . " 2>&1";
				exec($command, $output, $return_var);

				//bundle build failed
				if ($return_var != 0 || !in_array("Bundle created: $context->bundle_name.min.js", $output))
				{
					KExternalErrors::dieError(KExternalErrors::BUNDLE_CREATION_FAILED, $config);
				}
			}
			else
			{
				KExternalErrors::dieError(KExternalErrors::BUNDLE_CREATION_FAILED, $config);
			}
		}
	}

	private function getBundleContent($path)
	{
		$bundleContent = file_get_contents($path);
		$autoEmbed = $this->getRequestParameter('autoembed');
		$iframeEmbed = $this->getRequestParameter('iframeembed');

		//if auto embed selected add embed script to bundle content
		if ($autoEmbed) {
			$bundleContent .= $this->getAutoEmbedCode();
		} elseif ($iframeEmbed) {
			$bundleContent = $this->getIfarmEmbedCode($bundleContent);
		}

		$protocol = infraRequestUtils::getProtocol();
		$host = myPartnerUtils::getCdnHost($this->partnerId, $protocol, 'api');
		$loader = kConf::get('playkit_js_source_map_loader');
		$sourceMapLoaderURL = "$host/$loader/path/$this->bundle_name.min.js.map";
		$bundleContent = str_replace("//# sourceMappingURL=$this->bundle_name.min.js.map", "//# sourceMappingURL=$sourceMapLoaderURL", $bundleContent);

		return $bundleContent;
	}

	private function sendHeaders($content)
	{
		$max_age = 60 * 10;
		$lastModified = filemtime($this->bundlePath);
		// Support Etag and 304
		if (
			(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
				$_SERVER['HTTP_IF_MODIFIED_SINCE'] == infraRequestUtils::formatHttpTime($lastModified)) ||
			(isset($_SERVER['HTTP_IF_NONE_MATCH']) && @trim($_SERVER['HTTP_IF_NONE_MATCH']) == $this->getOutputHash($content))
		) {
			infraRequestUtils::sendCachingHeaders($max_age, false, $lastModified);
			header("HTTP/1.1 304 Not Modified");
			return;
		}

		$iframeEmbed = $this->getRequestParameter('iframeembed');
		if ($iframeEmbed) {
			header("Content-Type: text/html");
		} else {
			header("Content-Type: text/javascript");
		}

		header("Etag: " . $this->getOutputHash($content));
		// alwayse set cross orgin headers:
		header("Access-Control-Allow-Origin: *");
		infraRequestUtils::sendCachingHeaders($max_age, false, $lastModified);

	}

	private function getOutputHash($o)
	{
		if (!$this->eTagHash) {
			$this->eTagHash = md5($o);
		}
		return $this->eTagHash;
	}

	private function getAutoEmbedCode()
	{
		$config = json_encode($this->getRequestParameter("config"));
		$entry_id = $this->getRequestParameter('entry_id');
		$autoEmbedCode = "\n var player; var ovpProvider = new Providers.OvpProvider($this->partnerId,\"\",$config);\n" .
			"\t    ovpProvider.getConfig(\"" . $entry_id . "\",$this->uiconfId).then(config => {\n" .
			"\t    player = Playkit.playkit(config);\n" .
			"\t }, \n" .
			"\t err => {\n" .
			"\t    console.log(err)\n" .
			"\t})\n";

		return $autoEmbedCode;
	}

	private function getIfarmEmbedCode($bundleContent)
	{
		$bundleContent .= $this->getAutoEmbedCode();
		$htmlDoc = '<!DOCTYPE html PUBLIC " -//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html xmlns = "http://www.w3.org/1999/xhtml" >
                        <head >
                            <meta http - equiv = "Content-Type" content = "text/html; charset=iso-8859-1" />
                        </head >
                        <body >
                            <script type = "text/javascript" > ' . $bundleContent . '</script >
                        </body >
                    </html >';
		return $htmlDoc;
	}

	private function setLatestOrBetaVersionNumber($confVars)
	{
		//if latest/beta version required set version number in config obj
		$isLatestVersionRequired = strpos($confVars, "{latest}") !== false;
		$isBetaVersionRequired = strpos($confVars, "{beta}") !== false;

		if ($isLatestVersionRequired || $isBetaVersionRequired) {
			$latestVersionsMapPath = $this->sourcesPath . "/latest.json";
			$latestVersionMap = file_exists($latestVersionsMapPath) ? json_decode(file_get_contents($latestVersionsMapPath), true) : null;

			$betaVersionsMapPath = $this->sourcesPath . "/beta.json";
			$betatVersionMap = file_exists($betaVersionsMapPath) ? json_decode(file_get_contents($betaVersionsMapPath), true) : null;

			foreach ($this->bundleConfig as $key => $val) {
				if ($val == "{latest}" && $latestVersionMap != null) {
					$this->bundleConfig[$key] = $latestVersionMap[$key];
				}

				if ($val == "{beta}" && $betatVersionMap != null) {
					$this->bundleConfig[$key] = $betatVersionMap[$key];
				}
			}
		}
	}

	private function initMembers()
	{
		$this->eTagHash = null;

		//Get uiConf ID from QS
		$this->uiconfId = $this->getRequestParameter(self::UI_CONF_ID_PARAM_NAME);
		if (!$this->uiconfId)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, self::UI_CONF_ID_PARAM_NAME);

		// retrieve uiCong Obj
		$uiConf = uiConfPeer::retrieveByPK($this->uiconfId);
		if (!$uiConf)
			KExternalErrors::dieError(KExternalErrors::UI_CONF_NOT_FOUND);

		//Get bundle configuration stored in conf_vars
		$confVars = $uiConf->getConfVars();
		if (!$confVars) {
			KExternalErrors::dieGracefully("Missing bundle configuration in uiConf, uiConfID: $this->uiconfId");
		}

		//Get partner ID from QS or from UI conf
		$this->partnerId = $this->getRequestParameter(self::PARTNER_ID_PARAM_NAME, $uiConf->getPartnerId());

		//Get should force regenration
		$this->regenerate = $this->getRequestParameter('regenerate');

		//Get config params
		try {
			$this->bundleWebDirPath = rtrim(kConf::get('playkit_js_bundles_path'),"/");
			$this->bundleBuilderPath = kConf::get('bundle_builder_cli_path');
			$this->sourcesPath = kConf::get('playkit_js_sources_path');
		} catch (Exception $ex) {
			KExternalErrors::dieError(KExternalErrors::INTERNAL_SERVER_ERROR);
		}

		$this->bundleConfig = json_decode($confVars, true);
		$this->setLatestOrBetaVersionNumber($confVars);
		$this->setBundleName();
		$namePrefix = substr($this->bundle_name, 0, 2);
		$subNamePrefix = substr($this->bundle_name, 2, 2);
		$this->bundleWebDirPath = $this->bundleWebDirPath . "/" . $namePrefix . "/" . $subNamePrefix . "/";
		$this->bundlePath = $this->bundleWebDirPath . $this->bundle_name . ".min.js";
	}

	private function setBundleName()
	{
		//sort bundle config by key
		ksort($this->bundleConfig);

		//create base64 bundle name from json config
		$config_str = json_encode($this->bundleConfig);
		$this->bundle_name = base64_encode($config_str);
	}


}