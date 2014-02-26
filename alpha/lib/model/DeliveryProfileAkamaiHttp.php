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
		if($this->params->getExtention())
			$url .= "/name/a." . $this->params->getExtention();
		if($this->params->getSeekFromTime() > 0)
		{
			$fromTime = floor($this->params->getSeekFromTime() / 1000);

			/*
			 * Akamai servers fail to return subset of the last second of the video.
			 * The URL will return the two last seconds of the video in such cases. 
			 **/
			$entry = $flavorAsset->getentry();
			if($entry && $fromTime > ($entry->getDurationInt() - 1))
				$fromTime -= 1;

			// add offset only of intelliseek option is enabled
			$useIntelliseek = $this->getUseIntelliseek();
			if(!is_null($useIntelliseek))
				$url .= "?aktimeoffset=$fromTime";
		}
		return $url;
	}
	
	// doGetFileSyncUrl - Inherit from parent
}

