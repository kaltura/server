<?php
class kEdgeCastUrlManager extends kUrlManager
{	
	/**
	 * @param flavorAsset $flavorAsset
	 * @return string
	 */
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		$url = preg_replace('/^mp4:(\/)*/', 'mp4:', $url);
		// move version param to "behind" the flavor asset id
		$flavorAssetId = $flavorAsset->getId();
		$flavorIdStr = '/flavorId/'.$flavorAssetId;
		$url = str_replace($flavorIdStr, '', $url);
		$url = str_replace('serveFlavor', 'serveFlavor'.$flavorIdStr, $url);
		
		if ($this->protocol == PlaybackProtocol::HTTP)
		{
    		if ($this->extention) {
    			$url .= "/name/a.$this->extention";
    		}
    			
    		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
    		$url = $this->addEcSeek($url, $syncKey);
		}			
		return $url;
	}
	
	/**
	 * @param FileSync $fileSync
	 * @return string
	 */
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$url = parent::doGetFileSyncUrl($fileSync);
		$url = preg_replace('/^mp4:(\/)*/', 'mp4:', $url);
		if ($this->protocol == PlaybackProtocol::HTTP)
		{
    		$syncKey = kFileSyncUtils::getKeyForFileSync($fileSync);
    		$url = $this->addEcSeek($url, $syncKey);
		}
		return $url;
	}
	
	
	private function addEcSeek($url, $syncKey)
	{
        if (!empty($this->seekFromTime))
        {
            // remove default seekFrom parameter
            $url = preg_replace('/seekFrom\/-?[0-9]*\/?/', '', $url);
            $url = rtrim($url, '/');
            
            // check if seekFromTime is set to something significant
            if ($this->seekFromTime > 0)
            {
                // check if flv or not 		    
        	    $notFlvFormat = ($this->extention && strtolower($this->extention) != 'flv') || ($this->containerFormat && strtolower($this->containerFormat) != 'flash video');
        	    
        	    if ($notFlvFormat) {
        	        // not flv - add ec_seek value in seconds
        	        $url .= '?ec_seek='.($this->seekFromTime/1000); // convert milliseconds to seconds
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
