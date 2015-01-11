<?php

class DeliveryProfileAkamaiHttp extends DeliveryProfileHttp {
	
	public function setUseIntelliseek($v)
	{
		$this->putInCustomData("useIntelliseek", $v);
	}
	public function getUseIntelliseek()
	{
		return $this->getFromCustomData("useIntelliseek");
	}
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$url = $this->getBaseUrl($flavorAsset);
		if($this->params->getClipTo())
			$url .= "/clipTo/" . $this->params->getClipTo();
		if($this->params->getFileExtension())
			$url .= "/name/a." . $this->params->getFileExtension();
		
		// add offset only of intelliseek option is enabled
		$useIntelliseek = $this->getUseIntelliseek();
		if($useIntelliseek && ($this->params->getSeekFromTime() > 0))
		{
			$fromTime = floor($this->params->getSeekFromTime() / 1000);

			/*
			 * Akamai servers fail to return subset of the last second of the video.
			 * The URL will return the two last seconds of the video in such cases. 
			 **/
			$entry = $flavorAsset->getentry();
			if($entry && $fromTime > ($entry->getDurationInt() - 1))
				$fromTime -= 1;

			$url .= "?aktimeoffset=$fromTime";
		}
		return $url;
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$url = $fileSync->getFilePath();
		
		if($this->getUseIntelliseek() && ($this->params->getSeekFromTime() > 0))
		{
			$fromTime = floor($this->params->getSeekFromTime() / 1000);

			/*
			 * Akamai servers fail to return subset of the last second of the video.
			 * The URL will return the two last seconds of the video in such cases. 
			 **/
			$entry = entryPeer::retrieveByPK($this->params->getEntryId()); 
			if($entry)
				$fromTime = min($fromTime, $entry->getDurationInt() - 1);

			$url .= "?aktimeoffset=$fromTime";
		}
		return $url;
	}
}
