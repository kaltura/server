<?php

class DeliveryProfileGenericAppleHttp extends DeliveryProfileAppleHttp {
	
	// @_!! Externalize
	public function setPattern($v)
	{
		$this->putInCustomData("pattern", $v);
	}
	public function getPattern()
	{
		return $this->getFromCustomData("pattern");
	}
	
	// doGetFlavorAssetUrl - Use parent implementation
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$url = parent::doGetFileSyncUrl($fileSync);
		$pattern = $this->getPattern();
		if(is_null($pattern))
			$pattern = '/hls-vod/{url}.m3u8';
		return str_replace('{url}', $url, $pattern);
	}
}

