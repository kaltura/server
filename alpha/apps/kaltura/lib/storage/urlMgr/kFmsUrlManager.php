<?php
/**
 * @package Core
 * @subpackage storage.FMS
 */
class kFmsUrlManager extends kUrlManager
{
	const PARAM_PATTERN_URL_PATTERN = '%s_pattern';

	const PARAM_PATTERN_URL_PATTERN_HTTPS = '%s_pattern_https';

	const PARAM_PATTERN_TOKENIZER_CLASS = 'tokenizer_%s_class';

	const PARAM_PATTERN_TOKENIZER_ARG = 'tokenizer_%s_arg%s';

	/**
	 * @param FileSync $fileSync
	 * @return string
	 */
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$fileSync = kFileSyncUtils::resolve($fileSync);
		
		$url = parent::doGetFileSyncUrl($fileSync);
		$url = trim($url, '/');
		
		switch ($this->protocol)
		{
		case PlaybackProtocol::APPLE_HTTP:
			$pattern = isset($this->params["hls_pattern"]) ? $this->params["hls_pattern"] : '/hls-vod/{url}.m3u8';
			break;
		
		case PlaybackProtocol::HDS:
			$pattern = isset($this->params["hds_pattern"]) ? $this->params["hds_pattern"] : '/hds-vod/{url}.f4m';
			break;
			
		default:
			$pattern = isset($this->params["default_pattern"]) ? $this->params["default_pattern"] : '{url}'; 
			break;
		}

		return str_replace('{url}', $url, $pattern);
	}
	
	public function getRendererClass()
	{
		$paramName = 'renderer_class_' . $this->protocol;
		if (isset($this->params[$paramName]))
			return $this->params[$paramName];
		return null;
	}

	/**
	 * @param flavorAsset $flavorAsset
	 * @return string
	 */
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$partnerId = $flavorAsset->getPartnerId();
		$subpId = $flavorAsset->getentry()->getSubpId();
		$partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $subpId);
		$flavorAssetId = $flavorAsset->getId();

		$this->setFileExtension($flavorAsset->getFileExt());
		$this->setContainerFormat($flavorAsset->getContainerFormat());

		$versionString = $this->getFlavorVersionString($flavorAsset);
		$url = "$partnerPath/serveFlavor/entryId/".$flavorAsset->getEntryId()."{$versionString}/flavorId/$flavorAssetId";
		if($this->seekFromTime > 0)
			$url .= "/seekFrom/$this->seekFromTime";

		if($this->clipTo)
			$url .= "/clipTo/$this->clipTo";

		switch($this->protocol)
		{
			case PlaybackProtocol::RTMP:
				$url .= '/forceproxy/true';
				$flvExtension = $this->extention && strtolower($this->extention) == 'flv';
				$containerFlash = $this->containerFormat && strtolower($this->containerFormat) == 'flash video';
				if ($flvExtension || $containerFlash)
					$url .= "/name/a.flv";
				else
					$url .= "/name/a.mp4";
				break;
			default:
				if ($this->extention)
					$url .= "/name/a.".$this->extention;
				break;
		}

		$url = trim(str_replace('\\', '/', $url), '/');

		$pattern = $this->getUrlPatternForProtocol($this->protocol);

		if ($pattern)
			return str_replace('{url}', $url, $pattern);
		else
			return '/'.$url; // the trailing slash will force adding the host name to the url
	}

	/**
	 * load tokenizer according to the url manager params.
	 *
	 * tokenizer configuration for storage profile (json):
	 * {"key":"tokenizer_<protocol>_class","value":"<tokenizer class name>"},
	 * {"key":"tokenizer_<protocol>_arg0","value":"<constructor arg 0 value>"},
	 * {"key":"tokenizer_<protocol>_arg1","value":"<constructor arg 1 value>"}
	 *
	 * tokenizer configuration for url_manager.ini:
	 * params.tokenizer_<protocol>_class = <tokenizer class name>
	 * params.tokenizer_<protocol>_arg0 = <constructor arg 0 value>
	 * params.tokenizer_<protocol>_arg1 = <constructor arg 1 value>
	 *
	 * @return kUrlTokenizer|null
	 */
	public function getTokenizer()
	{
		$classKey = $this->getParamsKey(self::PARAM_PATTERN_TOKENIZER_CLASS, $this->protocol);
		if (!array_key_exists($classKey, $this->params) || !$this->params[$classKey])
			return null;

		$className = $this->params[$classKey];
		$params = array();
		$i = 0;
		while(true)
		{
			$argKey = $this->getParamsKey(self::PARAM_PATTERN_TOKENIZER_ARG, $this->protocol, $i);
			if(!array_key_exists($argKey, $this->params))
				break;
			$params[] = $this->params[$argKey];
			$i++;
		}
		$reflector = new ReflectionClass($className);
		return $reflector->newInstanceArgs($params);
	}

	/**
	 * checks whether this url manager should handle the request and returns the
	 * required security. for example <host name>+token and it would later be tested against
	 * partner delivery restrictions
	 *
	 * @return string
	 */
	public function identifyRequest()
	{
		if (!$this->params['secure_prefix'])
			return false;

		$securedUrl = $this->params['secure_prefix'];
		$requestHost = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : null;
		if (!$requestHost)
			$requestHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;

		$currentDeliveryHost = isset($this->params['http_header_host']) ? $this->params['http_header_host'] : null;
		if ($requestHost !== $currentDeliveryHost)
			return false;

		$uri = $_SERVER['REQUEST_URI'];
		if (strpos($uri, $securedUrl) === 0)
			$currentDeliveryHost .= '+token';

		return $currentDeliveryHost;
	}

	/**
	 * @param $protocol
	 * @return string|null
	 */
	protected function getUrlPatternForProtocol($protocol)
	{
		$patternKey = $this->getParamsKey(self::PARAM_PATTERN_URL_PATTERN, $protocol);
		$patternHttpsKey = $this->getParamsKey(self::PARAM_PATTERN_URL_PATTERN_HTTPS, $protocol);

		if ($this->isHttps() && isset($this->params[$patternHttpsKey]))
			return $this->params[$patternHttpsKey];
		elseif (isset($this->params[$patternKey]))
			return $this->params[$patternKey];
		else
			return isset($this->params["default_pattern"]) ? $this->params["default_pattern"] : null;
	}

	/**
	 * @return bool
	 */
	protected function isHttps()
	{
		return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
	}

	/**
	 * @param $pattern
	 * @return string
	 */
	protected function getParamsKey($pattern)
	{
		return call_user_func_array('sprintf', func_get_args());
	}
}
