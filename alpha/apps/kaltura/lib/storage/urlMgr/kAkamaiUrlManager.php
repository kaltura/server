<?php
class kAkamaiUrlManager extends kUrlManager
{

	/**
	 * Returns the URL path with the authorization token appended. See the
	 *   README for more details.
	 */
	function urlauth_gen_url($sUrl, $sParam, $nWindow,
	                         $sSalt, $sExtract, $nTime) {
	
		$sToken = $this->urlauth_gen_token($sUrl, $nWindow, $sSalt,
	                                    $sExtract, $nTime);
		if ($sToken == null) {
			return;
		}
	
		if (($sParam == "") || (!is_string($sParam))) {
			$sParam = "__gda__";
		}
	
	    if ((strlen($sParam) < 5) || (strlen($sParam) > 12)) {
	        return;
	    }
	
		if (($nWindow < 0) || (!is_integer($nWindow))) {
			return;
		}
	
		if (($nTime <= 0) || (!is_integer($nTime))) {
			$nTime = time();
		}
	
		$nExpires = $nWindow + $nTime;
	
		if (strpos($sUrl, "?") === false) {
			$res = $sUrl . "?" . $sParam . "=" . $nExpires . "_" . $sToken;
		} else {
			$res = $sUrl . "&" . $sParam . "=" . $nExpires . "_" . $sToken;
		}
	
		return $res;
	}
	
	/**
	 * Returns the hash portion of the token. This function should not be
	 *   called directly.
	 */
	function urlauth_gen_token($sUrl, $nWindow, $sSalt,
	                           $sExtract, $nTime) {
		if (($sUrl == "") || (!is_string($sUrl))) {
			return;
		}
	
		if (($nWindow < 0) || (!is_integer($nWindow))) {
			return;
		}
	
		if (($sSalt == "") || (!is_string($sSalt))) {
			return;
		}
	
		if (!is_string($sExtract)) {
			$sExtract = "";
		}
	
		if (($nTime <= 0) || (!is_integer($nTime))) {
			$nTime = time();
		}
	
		$nExpires = $nWindow + $nTime;
		$sExpByte1 = chr($nExpires & 0xff);
		$sExpByte2 = chr(($nExpires >> 8) & 0xff);
		$sExpByte3 = chr(($nExpires >> 16) & 0xff);
		$sExpByte4 = chr(($nExpires >> 24) & 0xff);
	
		$sData = $sExpByte1 . $sExpByte2 . $sExpByte3 . $sExpByte4
	                 . $sUrl . $sExtract . $sSalt;
	
		$sHash = $this->_unHex(md5($sData));
	
		$sToken = md5($sSalt . $sHash);
		return $sToken;
	}
	
	/**
	 * Helper function used to translate hex data to binary
	 */
	function _unHex($str) {
		$res = "";
		for ($i = 0; $i < strlen($str); $i += 2) {
	        $res .= chr(hexdec(substr($str, $i, 2)));
		}
		return $res;
	}

	/**
	 * @param flavorAsset $flavorAsset
	 * @return string
	 */
	public function getFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$partnerId = $flavorAsset->getPartnerId();
		$subpId = $flavorAsset->getentry()->getSubpId();
		$flavorAssetId = $flavorAsset->getId();
		$partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $subpId);
		
		$this->setFileExtension($flavorAsset->getFileExt());
		$this->setContainerFormat($flavorAsset->getContainerFormat());	

		$url = "$partnerPath/serveFlavor/flavorId/$flavorAssetId";
		
		if($this->clipTo)
			$url .= "/clipTo/$this->clipTo";

		if($this->extention)
			$url .= "/name/$flavorAssetId.$this->extention";
					
		if($this->protocol == StorageProfile::PLAY_FORMAT_RTMP)
		{
			$url .= '/forceproxy/true';
			if($this->extention && strtolower($this->extention) != 'flv' ||
				$this->containerFormat && strtolower($this->containerFormat) != 'flash video')
				$url = "mp4:$url";
		}
		else
		{		
			if($this->seekFromTime > 0)
				$url .= '?aktimeoffset=' . floor($this->seekFromTime / 1000);
		}
			
		$url = str_replace('\\', '/', $url);

		if ($this->protocol == StorageProfile::PLAY_FORMAT_HTTP && @$this->params['http_auth_salt'])
		{
			$window = $this->params['http_auth_seconds'];
			$param = $this->params['http_auth_param'];
			$salt = $this->params['http_auth_salt'];
			$root_dir = $this->params['http_auth_root_dir'];
			$url = $this->urlauth_gen_url($root_dir.$url, $param, $window, $salt, null, null);
		}

		return $url;
	}
	
	/**
	 * @return boolean
	 */
	public function authenticateRequest($url)
	{
		return authenticateRequestUrl($_SERVER["SCRIPT_URL"]);
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
		
		//'auth_data_header' => 'HTTP_X_AKAMAI_G2O_AUTH_DATA' 
		//'auth_sign_header' => 'HTTP_X_AKAMAI_G2O_AUTH_SIGN' 
		$authData = $this->params[$authDataHeader];
		$authSign = $this->params[$authSignHeader];
		$window = $this->params['smooth_auth_seconds'];
		$param = $this->params['smooth_auth_param'];
		$salt = $this->params['smooth_auth_salt'];
		
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
	public function getFileSyncUrl(FileSync $fileSync)
	{
		$fileSync = kFileSyncUtils::resolve($fileSync);
		
		$storage = StorageProfilePeer::retrieveByPK($fileSync->getDc());
		if(!$storage)
			return parent::getFileSyncUrl($fileSync);
		
		$serverUrl = $storage->getDeliveryIisBaseUrl();
		$partnerPath = myPartnerUtils::getUrlForPartner($fileSync->getPartnerId(), $fileSync->getPartnerId() * 100);
		
		if ($this->$protocol == StorageProfile::PLAY_FORMAT_APPLE_HTTP)
			return $partnerPath.$fileSync->getFilePath()."/playlist.m3u8";
		
		if($fileSync->getObjectSubType() != entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM)
			return parent::getFileSyncUrl($fileSync);
		
		$path = $partnerPath.'/serveIsm/objectId/' . $fileSync->getObjectId() . '_' . $fileSync->getObjectSubType() . '_' . $fileSync->getVersion() . '.' . pathinfo(kFileSyncUtils::resolve($fileSync)->getFilePath(), PATHINFO_EXTENSION) . '/manifest';
//		$path = $partnerPath.'/serveIsm/objectId/'.pathinfo(kFileSyncUtils::resolve($fileSync)->getFilePath(), PATHINFO_BASENAME).'/manifest';		
		$matches = null;
		if(preg_match('/(https?:\/\/[^\/]+)(.*)/', $serverUrl, $matches))
		{
			$serverUrl = $matches[1];
			$path = $matches[2] . $path;
		}
		
		$path = str_replace('//', '/', $path);

		$window = $this->params['smooth_auth_seconds'];
		$param = $this->params['smooth_auth_param'];
		$salt = $this->params['smooth_auth_salt'];
		$authPath = $this->urlauth_gen_url($path, $param, $window, $salt, null, null);
		
		return $serverUrl . '/' . $authPath;
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
		$delivery = @$_SERVER['HTTP_HOST'];
		if ($delivery != @$this->params["http_header_host"])
			return false;
		
		$uri = $_SERVER["REQUEST_URI"];
		if (strpos($uri, "/s/") === 0)
			$delivery .= "+token";
			
		return $delivery;
	}

}
