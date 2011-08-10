<?php
class kLevel3UrlManager extends kUrlManager
{
	static private function hmac($hashfunc, $key, $data)
	{
		$blocksize=64;

		if (strlen($key) > $blocksize)
		{
			$key = pack('H*', $hashfunc($key));
		}

		$key = str_pad($key, $blocksize, chr(0x00));
		$ipad = str_repeat(chr(0x36), $blocksize);
		$opad = str_repeat(chr(0x5c), $blocksize);
		$hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));

		return bin2hex($hmac);
	}
	
	/**
	 * @param flavorAsset $flavorAsset
	 * @return string
	 */
	public function getFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$entry = $flavorAsset->getentry();
		$partnerId = $entry->getPartnerId();
		$subpId = $entry->getSubpId();
		$flavorAssetId = $flavorAsset->getId();
		$flavorAssetVersion = $flavorAsset->getVersion();
		$partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $subpId);
		
		$this->setFileExtension($flavorAsset->getFileExt());

		$versionString = (!$flavorAssetVersion || $flavorAssetVersion == 1 ? '' : "/v/$flavorAssetVersion");
		$url = "$partnerPath/serveFlavor{$versionString}/flavorId/$flavorAssetId";
		
		if($this->clipTo)
			$url .= "/clipTo/$this->clipTo";

		if($this->extention)
			$url .= "/name/$flavorAssetId.$this->extention";
					
		if($this->protocol != StorageProfile::PLAY_FORMAT_RTMP)
		{	
			$url .= '?novar=0';
				
			if ($entry->getSecurityPolicy())
			{
				$url = "/s$url";
				
				// set expire time in GMT hence the date("Z") offset
				$url .= "&nva=" . strftime("%Y%m%d%H%M%S", time() - date("Z") + 30);
			}
		
			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$seekFromBytes = $this->getSeekFromBytes(kFileSyncUtils::getLocalFilePathForKey($syncKey));
			if($seekFromBytes)
				$url .= '&start=' . $seekFromBytes;
				
    		// if level3 tokenized url is used for http, generate token string
    		if($this->protocol == StorageProfile::PLAY_FORMAT_HTTP)
    		{
    		    $name = isset($this->params['http_auth_param_name']) ? $this->params['http_auth_param_name'] : "h";
    		    $key = isset($this->params['http_auth_key']) ? $this->params['http_auth_key'] : false;
    		    $gen = isset($this->params['http_auth_gen']) ? $this->params['http_auth_gen'] : false;
    		    
    			$url = $this->tokenizeUrl($url, $name, $key, $gen);
    		}
		}
		else
		{
			if($this->extention && strtolower($this->extention) != 'flv' ||
				$this->containerFormat && strtolower($this->containerFormat) != 'flash video')
				$url = "mp4:$url";
		}
				
		$url = str_replace('\\', '/', $url);
		return $url;
	}
	
	/**
	 * @param FileSync $fileSync
	 * @return string
	 */
	public function getFileSyncUrl(FileSync $fileSync)
	{
		$url = parent::getFileSyncUrl($fileSync);
		
	    // if level3 tokenized url is used for http, generate token string
		if($this->protocol == StorageProfile::PLAY_FORMAT_HTTP)
		{
		    $name = isset($this->params['http_auth_param_name']) ? $this->params['http_auth_param_name'] : "h";
		    $key = isset($this->params['http_auth_key']) ? $this->params['http_auth_key'] : false;
		    $gen = isset($this->params['http_auth_gen']) ? $this->params['http_auth_gen'] : false;
		    
			$url = $this->tokenizeUrl($url, $name, $key, $gen);
		}
				
		return $url;
	}
	
	

	protected function tokenizeUrl($url, $name, $key, $gen, $baseUrl = null)
	{
		if ($name && $key !== false && $gen !== false)
		{
		$url = preg_replace('/([^:])\/\//','$1/', $url);
    		$fullUrl = trim(str_replace('mp4:', '', $url), '/');
    	    if (!is_null($baseUrl)) {
    	        $fullUrl = rtrim($baseUrl, '/').'/'.$fullUrl;
    	    }
    	    if ($this->protocol == StorageProfile::PLAY_FORMAT_RTMP)
		    {
		        $fullUrl .= '.'.$this->extention;
		    }
		    
		    $parsedUrl = parse_url($fullUrl);
		    $pathString = '/'.ltrim($parsedUrl['path'],'/');

		    $token = substr(self::hmac('sha1', $key, $pathString), 0, 20);
		    
		    if (isset($parsedUrl['query ']) && strlen($parsedUrl['query']) > 0) {
		        $url .= "&$name=$gen".$token;
		    }
		    else {
		        $url .= "?$name=$gen".$token;
		    }
		}
		return $url;
	}
	
	/**
	 * @param string baseUrl
	 * @param array $flavorUrls
	 */
	public function finalizeUrls(&$baseUrl, &$flavorsUrls)
	{
	    // if level3 tokenized url is used for rtmp, generate token string
		if($this->protocol == StorageProfile::PLAY_FORMAT_RTMP)
		{
		    $name = isset($this->params['rtmp_auth_param_name']) ? $this->params['rtmp_auth_param_name'] : "h";
		    $key = isset($this->params['rtmp_auth_key']) ? $this->params['rtmp_auth_key'] : false;
		    $gen = isset($this->params['rtmp_auth_gen']) ? $this->params['rtmp_auth_gen'] : false;
		    
		    // tokenize flavor urls
            foreach($flavorsUrls as $flavorKey => $flavor)
    		{
    			if (isset($flavor['url']) && $flavor['url'])
    			{
    			    $flavorsUrls[$flavorKey ]['url'] = $this->tokenizeUrl($flavor['url'], $name, $key, $gen, $baseUrl);
    			}
    		} 
		}	    
	}
	
	
}
