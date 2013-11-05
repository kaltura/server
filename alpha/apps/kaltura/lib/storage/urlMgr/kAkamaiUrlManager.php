<?php
/**
 * @package Core
 * @subpackage storage.Akamai
 */
class kAkamaiUrlManager extends kUrlManager
{
	const SECURE_HD_AUTH_ACL_REGEX = '/^[^,]*/';
	
	/**
	 * @return kUrlTokenizer
	 */
	public function getTokenizer()
	{
		switch ($this->protocol)
		{
		case PlaybackProtocol::HTTP:
			if (isset($this->params['http_auth_salt']) && $this->params['http_auth_salt'])
			{
				return new kAkamaiHttpUrlTokenizer(
					$this->params['http_auth_seconds'],
					$this->params['http_auth_param'],
					$this->params['http_auth_salt'],
					isset($this->params['http_auth_root_dir']) ? $this->params['http_auth_root_dir'] : '');
			}
			break;
			
		case PlaybackProtocol::RTMP:
			if (isset($this->params['rtmp_auth_salt']) && $this->params['rtmp_auth_salt'])
			{
				return new kAkamaiRtmpUrlTokenizer(
					$this->params['rtmp_auth_profile'],
					$this->params['rtmp_auth_type'],
					$this->params['rtmp_auth_salt'],
					$this->params['rtmp_auth_seconds'],
					$this->params['rtmp_auth_aifp'],
					@$this->params['rtmp_auth_slist_find_prefix']);
			}
			break;
		
		case PlaybackProtocol::RTSP:
			return new kAkamaiRtspUrlTokenizer(
				$this->params["rtsp_host"],
				$this->params["rtsp_cpcode"]);

		case PlaybackProtocol::SILVER_LIGHT:
			if (isset($this->params['smooth_auth_salt']) && $this->params['smooth_auth_salt'])
			{
				return new kAkamaiHttpUrlTokenizer(
					$this->params['smooth_auth_seconds'],
					$this->params['smooth_auth_param'],
					$this->params['smooth_auth_salt'],
					'');
			}
			break;

		case PlaybackProtocol::APPLE_HTTP:
			if (!isset($this->params["hd_secure_ios"]))
			{
				break;
			}
			
		case PlaybackProtocol::AKAMAI_HDS:
			if (isset($this->params['secure_hd_auth_salt']) && $this->params['secure_hd_auth_salt'])
			{
				return new kAkamaiSecureHDUrlTokenizer(
					$this->params['secure_hd_auth_seconds'],
					$this->params['secure_hd_auth_param'],
					self::SECURE_HD_AUTH_ACL_REGEX,
					$this->params['secure_hd_auth_acl_postfix'],
					$this->params['secure_hd_auth_salt']);
			}
			break;
		}
		
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
		$flavorAssetId = $flavorAsset->getId();

		$partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $subpId);
		
		$this->setFileExtension($flavorAsset->getFileExt());
		$this->setContainerFormat($flavorAsset->getContainerFormat());	

		$versionString = $this->getFlavorVersionString($flavorAsset);
		$url = "$partnerPath/serveFlavor/entryId/".$flavorAsset->getEntryId()."{$versionString}/flavorId/$flavorAssetId";
		if($this->protocol==PlaybackProtocol::RTSP) {
			return $url;
		}
	
		if($this->protocol==PlaybackProtocol::APPLE_HTTP) {
			if (strpos($flavorAsset->getTags(), flavorParams::TAG_APPLEMBR) === FALSE)
			{
				// we use index_0_av.m3u8 instead of master.m3u8 as temporary solution to overcome
				// an extra "redirection" done on the part of akamai.
				// the auto created master.m3u8 file contains a single item playlist to the index_0_av.m3u8 file
				// this extra "redirection" fails
				$url = "http://".@$this->params['hd_ios']."/i".$url."/index_0_av.m3u8";				
			}
			else
				$url .= "/file/playlist.m3u8";
		}
		else {
			if($this->clipTo)
				$url .= "/clipTo/$this->clipTo";

			if($this->protocol == "hdnetworksmil" && isset($this->params["hd_flash"]))
			{
				$url = "http://".$this->params["hd_flash"].$url.'/forceproxy/true';
			}
			else if($this->protocol == PlaybackProtocol::RTMP)
			{
				$url .= '/forceproxy/true';
				$url = trim($url, "/");
				if($this->extention && strtolower($this->extention) != 'flv' ||
					$this->containerFormat && strtolower($this->containerFormat) != 'flash video')
					$url = "mp4:$url";
			}
			else
			{
				if ($this->protocol == PlaybackProtocol::AKAMAI_HDS)
					$url .= '/forceproxy/true';
				
				if($this->extention)
					$url .= "/name/a.$this->extention";
						
				if($this->seekFromTime > 0)
				{
					$fromTime = floor($this->seekFromTime / 1000);
					
					/*
					 * Akamai servers fail to return subset of the last second of the video.
					 * The URL will return the two last seconds of the video in such cases. 
					 **/
					$entry = $flavorAsset->getentry();
					if($entry && $fromTime > ($entry->getDurationInt() - 1))
						$fromTime -= 1;
						
					// add offset only of intelliseek option is enabled
					if(isset($this->params['enable_intelliseek']))
						$url .= "?aktimeoffset=$fromTime";
				}
			}
		}
		
		$url = str_replace('\\', '/', $url);

		return $url;
	}
	
	/**
	 * @return boolean
	 */
	public function authenticateRequest($url)
	{
		return $this->authenticateRequestUrl($_SERVER["SCRIPT_URL"]);
	}
	
	/**
	 * @return boolean
	 */
	public function authenticateRequestUrl($url)
	{
		$authHeaderData = $this->params['auth_header_data'];
		$authHeaderSign = $this->params['auth_header_sign'];
		$authHeaderTimeout = $this->params['auth_header_timeout'];
		$authHeaderSalt = $this->params['auth_header_salt'];
		
		$authData = @$_SERVER[$authHeaderData];
		$authSign = @$_SERVER[$authHeaderSign];
		
		list($version, $ghostIp, $clientIp, $time, $uniqueId, $nonce) = explode(",", $authData);
		if ($authHeaderTimeout) {
		    // Compare the absolute value of the difference between the current time
		    // and the "token" time.
			if (abs(time() - $time) > $authHeaderTimeout ) {
				return false;
			}
		}
		
		$newSign = base64_encode(md5($authHeaderSalt . $authData . $url, true));		
        if ($newSign == $authSign) {
            return true;
        }

        return false;
	}

	/**
	 * @param FileSync $fileSync
	 * @return string
	 */
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$fileSync = kFileSyncUtils::resolve($fileSync);
		
		$storage = StorageProfilePeer::retrieveByPK($fileSync->getDc());
		if(!$storage)
			return parent::doGetFileSyncUrl($fileSync);
	
		$serverUrl = $storage->getDeliveryIisBaseUrl();
		$partnerPath = myPartnerUtils::getUrlForPartner($fileSync->getPartnerId(), $fileSync->getPartnerId() * 100);
		
		if ($this->protocol == PlaybackProtocol::APPLE_HTTP && isset($this->params["hd_ios"])){
			$path = $fileSync->getFilePath();
			$urlSuffix = str_replace('\\', '/', $path)."/index_0_av.m3u8";
			$urlPrefix = "http://".$this->params["hd_ios"].'/i/';
			return $urlPrefix.ltrim($urlSuffix, '/');
		}
		
		if($this->protocol == "hdnetworksmil" && isset($this->params["hd_flash"])){
			$path = $fileSync->getFilePath();
			$urlSuffix = str_replace('\\', '/', $path);
			$urlPrefix = "http://".$this->params["hd_flash"];
			return $urlPrefix. '/' . ltrim($urlSuffix, '/');
		}
		
		if($fileSync->getObjectSubType() != entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM)
			return parent::doGetFileSyncUrl($fileSync);

		$serverUrl = myPartnerUtils::getIisHost($fileSync->getPartnerId(), "http");	
		
		$path = $partnerPath.'/serveIsm/objectId/' . $fileSync->getObjectId() . '_' . $fileSync->getObjectSubType() . '_' . $fileSync->getVersion() . '.' . pathinfo(kFileSyncUtils::resolve($fileSync)->getFilePath(), PATHINFO_EXTENSION) . '/manifest';
		$matches = null;
		if(preg_match('/(https?:\/\/[^\/]+)(.*)/', $serverUrl, $matches))
		{
			$path = $matches[2] . $path;
		}
		
		$path = str_replace('//', '/', $path);
	
		return $path;
	}

	/**
	 * check whether this url manager sent the current request.
	 * if so, return a string describing the usage. e.g. cdn.acme.com+token for
	 * using cdn.acme.com with secure token delivery. This string can be matched to the
	 * partner settings in order to enforce a specific delivery method. 
	 * @return string
	 */
	public function identifyRequest()
	{
		$delivery = @$_SERVER['HTTP_X_FORWARDED_HOST'];
		if (!$delivery)
			$delivery = @$_SERVER['HTTP_HOST'];
		if ($delivery != @$this->params["http_header_host"])
			return false;
		
		$uri = $_SERVER["REQUEST_URI"];
		if (strpos($uri, "/s/") === 0)
			$delivery .= "+token";
			
		return $delivery;
	}

	private function generateCsmilUrl(array $flavors)
	{
		$urls = array();
		foreach ($flavors as $flavor)
		{
			$urls[] = $flavor['url'];
		}
		$urls = array_unique($urls);

		if (count($urls) == 1)
		{
			$baseUrl = reset($urls);
			return '/' . ltrim($baseUrl, '/');
		}

		$prefix = kString::getCommonPrefix($urls);
		$prefixLen = strlen($prefix);
		$postfix = kString::getCommonPostfix($urls);
		$postfixLen = strlen($postfix);
		$middlePart = ',';
		foreach ($urls as $url)
		{
			$middlePart .= substr($url, $prefixLen, strlen($url) - $prefixLen - $postfixLen) . ',';
		}
		$baseUrl = $prefix . $middlePart . $postfix;

		return '/' . ltrim($baseUrl, '/') . '.csmil';
	}

	public function getManifestUrl(array $flavors)
	{
		$url = $this->generateCsmilUrl($flavors);
		
		if ($this->protocol == PlaybackProtocol::APPLE_HTTP)
		{
			if (!isset($this->params["hd_secure_ios"]))
				return null;

			$protocolFolder = '/i';
			$url = $url . '/master.m3u8';
			$urlPrefix = $this->params["hd_secure_ios"];
		}
		else
		{
			if (!isset($this->params["hd_secure_hds"]))
				return null;
				
			$protocolFolder = '/z';
			$url = $url . '/manifest.f4m';		
			$urlPrefix = $this->params["hd_secure_hds"];
		}

		// move any folders on the url prefix to the url part, so that the protocol folder will always be first
		$urlPrefixWithProtocol = $urlPrefix;
		if (strpos($urlPrefix, '://') === false)
			$urlPrefixWithProtocol = 'http://' . $urlPrefix;
		
		$urlPrefixPath = parse_url($urlPrefixWithProtocol, PHP_URL_PATH);
		if ($urlPrefixPath && substr($urlPrefix, -strlen($urlPrefixPath)) == $urlPrefixPath)
		{
			$urlPrefix = substr($urlPrefix, 0, -strlen($urlPrefixPath));
			$url = rtrim($urlPrefixPath, '/') . '/' . ltrim($url, '/');
		}
		
		return array('url' => $protocolFolder . $url, 'urlPrefix' => $urlPrefix);		
	}
}
	
