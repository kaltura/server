<?php

class DeliveryProfileGenericSilverLight extends DeliveryProfileSilverLight {
	
	public function setPattern($v)
	{
		$this->putInCustomData("pattern", $v);
	}
	public function getPattern()
	{
		return $this->getFromCustomData("pattern");
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$url = parent::doGetFileSyncUrl($fileSync);
		$pattern = $this->getPattern();
		if(is_null($pattern))
			$pattern = '{url}';
		return kDeliveryUtils::formatGenericUrl($url, $pattern, $this->params);
	}
}

