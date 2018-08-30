<?php

class DeliveryProfileVodPackagerHls extends DeliveryProfileAppleHttp {
	
	protected function doGetFlavorAssetUrl(asset $flavorAsset) 
	{
		$url = $this->getBaseUrl($flavorAsset);
		if ($this->params->getFileExtension())
			$url .= "/name/a." . $this->params->getFileExtension();
		$url .= VodPackagerDeliveryUtils::getExtraParams($this->params);
		return $url . '/index.m3u8';
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$url = parent::doGetFileSyncUrl($fileSync);
		
		$url .= VodPackagerDeliveryUtils::getExtraParams($this->params);
		return $url . '/index.m3u8';
	}

	protected function getPlayServerUrl()
	{
		return $this->generatePlayServerUrl();
	}

	public function setAllowFairplayOffline($v)
	{
		$this->putInCustomData("allowFairplayOffline", $v);
	}

	public function getAllowFairplayOffline()
	{
		return $this->getFromCustomData("allowFairplayOffline", null, false);
	}

	/**
	 * @param bool $oneOnly
	 * @return array
	 */
	protected function buildHttpFlavorsArray()
	{
		$flavors = array();
		foreach($this->params->getflavorAssets() as $flavorAsset)
		{
			if ($this->params->getEdgeServerIds() && count($this->params->getEdgeServerIds()))
			{
				foreach ($this->params->getEdgeServerIds() as $currEdgeServerId)
				{
					$serverNode = ServerNodePeer::retrieveByPK($currEdgeServerId);
					/** @var EdgeServerNode $currEdgeServer */
					$httpUrl = $this->getFlavorHttpUrl($flavorAsset);
					if ($httpUrl)
						$flavors[] = $httpUrl;

				}
			}
		}
		foreach($this->params->getflavorAssets() as $flavorAsset)
		{
			/* @var $flavorAsset asset */
			$httpUrl = $this->getFlavorHttpUrl($flavorAsset);
			if ($httpUrl)
				$flavors[] = $httpUrl;
		}
		return $flavors;
	}


}
