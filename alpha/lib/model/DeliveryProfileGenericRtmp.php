<?php

class DeliveryProfileGenericRtmp extends DeliveryProfileRtmp {

	protected $REDUNDANT_EXTENSIONS = array();

	public function setPattern($v)
	{
		$this->putInCustomData("pattern", $v);
	}
	
	public function getPattern()
	{
		return $this->getFromCustomData("pattern");
	}
	
	public function setRendererClass($v)
	{
		$this->putInCustomData("rendererClass", $v);
	}
	
	public function getRendererClass()
	{
		return $this->getFromCustomData("rendererClass", null , $this->DEFAULT_RENDERER_CLASS);
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
		return $url;
	}
}

