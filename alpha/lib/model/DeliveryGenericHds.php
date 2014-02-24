<?php

class DeliveryGenericHds extends DeliveryHds {
	
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
			$pattern = '/hds-vod/{url}.f4m';
		return str_replace('{url}', $url, $pattern);
	}
}

