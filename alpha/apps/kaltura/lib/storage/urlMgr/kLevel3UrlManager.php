<?php
class kLevel3UrlManager extends kUrlManager
{
    const TOKENIZED_RTMP_PARAM = 'level3_tokenized_rtmp';
    const LEVEL3_ID_PARAM = 'level3_id';
    const LEVEL3_SECRET_PARAM = 'level3_secret';    
    
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
	
		$versionString = ($flavorAssetVersion == 1 ? '' : "/v/$flavorAssetVersion");
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
				
				$secret = kConf::get("level3_authentication_key");
				$hash = "0" . substr(self::hmac('sha1', $secret, $url), 0, 20);
				$url .= "&h=$hash"; 
			}
		
			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$seekFromBytes = $this->getSeekFromBytes(kFileSyncUtils::getLocalFilePathForKey($syncKey));
			if($seekFromBytes)
				$url .= '&start=' . $seekFromBytes;
		}
		else
		{
			$url .= '/forceproxy/true';
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
	    // get params
		$tokenizedRtmp = isset($this->params[self::TOKENIZED_RTMP_PARAM]) ? $this->params[self::TOKENIZED_RTMP_PARAM] : false;
		$level3Id      = isset($this->params[self::LEVEL3_ID_PARAM]) ? $this->params[self::LEVEL3_ID_PARAM] : false;
		$secret        = isset($this->params[self::LEVEL3_SECRET_PARAM]) ? $this->params[self::LEVEL3_SECRET_PARAM] : false;
	    
		// get url from parent
	    $url = parent::getFileSyncUrl($fileSync);
	    
	    // if level3 tokenized url is used for rtmp, generated token string
	    if($this->protocol == StorageProfile::PLAY_FORMAT_RTMP)
		{
    	    if($tokenizedRtmp && $level3Id && $secret)
    		{
        		$urlToTokenize = str_replace('mp4:', '', $url);
        		$urlToTokenize = '/'.$level3Id.'/'.trim($urlToTokenize, '/').'.'.$this->extention;
        		
                $token = "0" . substr(hash_hmac('sha1', $urlToTokenize, $secret), 0, 20);
        	
                $url = $url.'?token='.$token;
    		}
		}

		return $url;
	}
}