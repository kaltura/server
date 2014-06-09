<?php

class DeliveryProfileGenericRtmp extends DeliveryProfileRtmp {
	
	// Whenever someone wants - you can extract that as parameter.
	private $removeDefaultPrefix = true;
	
	public function setPattern($v)
	{
		$this->putInCustomData("pattern", $v);
	}
	
	public function getPattern()
	{
		return $this->getFromCustomData("pattern");
	}
	
	public function setRendererClassParam($v)
	{
		$this->putInCustomData("rendererClass", $v);
	}
	
	public function getRendererClassParam()
	{
		return $this->getFromCustomData("rendererClass");
	}
	
	protected function getRendererClass() {
		$rendererClass = $this->getRendererClassParam();
		if($rendererClass)
			return $rendererClass;
		return $this->DEFAULT_RENDERER_CLASS;
	}
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset) 
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		$pattern = $this->getPattern();
		if(is_null($pattern))
			$pattern = '{$url}';
		return kDeliveryUtils::formatGenericUrl($url, $pattern, $this->params);
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$url = parent::doGetFileSyncUrl($fileSync);
		$pattern = $this->getPattern();
		if(is_null($pattern))
			$pattern = '{url}';
		return kDeliveryUtils::formatGenericUrl($url, $pattern, $this->params);
	}
	
	protected function formatByExtension($url) {
		$url = parent::formatByExtension($url);
		if($this->removeDefaultPrefix)
			$url = str_replace("mp4:", '', $url);
		return $url;
	}
}

