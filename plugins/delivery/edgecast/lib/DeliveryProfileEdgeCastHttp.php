<?php
class DeliveryProfileEdgeCastHttp extends DeliveryProfileHttp
{	
	/**
	 * @param flavorAsset $flavorAsset
	 * @return string
	 */
	protected function doGetFlavorAssetUrl(asset $flavorAsset)
	{
		$url = $this->getBaseUrl($flavorAsset);
		if($this->params->getClipTo())
		{
			$url = self::insertAfter($url, 'entryId', 'clipTo', $this->params->getClipTo());
		}

		if($this->params->getFileExtension())
			$url .= "/name/a.".$this->params->getFileExtension();
		
		$flavorAssetId = $flavorAsset->getId();
		$flavorIdStr = '/flavorId/'.$flavorAssetId;
		$url = str_replace($flavorIdStr, '', $url);
		$url = str_replace('serveFlavor', 'serveFlavor'.$flavorIdStr, $url);
		
    	$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
    	return $this->addEcSeek($url, $syncKey);
	}
	
	/**
	 * @param FileSync $fileSync
	 * @return string
	 */
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		
		$url = parent::doGetFileSyncUrl($fileSync);
		$url = preg_replace('/^mp4:(\/)*/', 'mp4:', $url);
		if ($this->params->getFormat() == PlaybackProtocol::HTTP)
		{
    		$syncKey = kFileSyncUtils::getKeyForFileSync($fileSync);
    		$url = $this->addEcSeek($url, $syncKey);
		}
		return $url;
	}
	
	
	private function addEcSeek($url, $syncKey)
	{
		$seekTime = $this->params->getSeekFromTime();
        if (!empty($seekTime))
        {
            // remove default seekFrom parameter
            $url = preg_replace('/seekFrom\/-?[0-9]*\/?/', '', $url);
            $url = rtrim($url, '/');
            
            // check if seekFromTime is set to something significant
            if ($seekTime > 0)
            {
                // check if flv or not 	
                $extension = $this->params->getFileExtension();	
                $containerFormat = $this->params->getContainerFormat();    
        	    $notFlvFormat = ($extension && strtolower($extension) != 'flv') || ($containerFormat && strtolower($containerFormat) != 'flash video');
        	    
        	    if ($notFlvFormat) {
        	        // not flv - add ec_seek value in seconds
        	        $url .= '?ec_seek='.($seekTime/1000); // convert milliseconds to seconds
        	    }
        	    else {
        	        // flv - add ec_seek value in bytes
        	        $url .= '?ec_seek='.$this->getSeekFromBytes(kFileSyncUtils::getLocalFilePathForKey($syncKey));
        	    }
            }
        }        
		return $url;
	}
	
}
