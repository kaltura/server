<?php

class DeliveryProfileAkamaiHttp extends DeliveryProfileHttp {

	const NAME_A = "/name/a.";

	public function setUseIntelliseek($v)
	{
		$this->putInCustomData("useIntelliseek", $v);
	}
	public function getUseIntelliseek()
	{
		return $this->getFromCustomData("useIntelliseek");
	}
	
	protected function doGetFlavorAssetUrl(asset $flavorAsset)
	{
		$url = $this->getBaseUrl($flavorAsset);
		if($this->params->getClipTo())
			$url .= "/clipTo/" . $this->params->getClipTo();
		else 
			$url .= '/forceproxy/true';
		
		if($this->params->getFileExtension())
			$url .= self::NAME_A . $this->params->getFileExtension();
		else
			$url .= $this->extractExtensionFromContainerFormat($this->params->getContainerFormat());
		
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

	/**
	 * Node, we do not use switch case as format, example: 'mp42 (isom/mp42)'
	 * @param string $containerFormat containerFormat
	 * @return string extension format
	 */
	private function extractExtensionFromContainerFormat($containerFormat)
	{
		if(strpos($containerFormat, assetParams::CONTAINER_FORMAT_MP42) === 0)
			return self::NAME_A . assetParams::CONTAINER_FORMAT_MP4;
		if(strpos($containerFormat, assetParams::CONTAINER_FORMAT_ISOM) === 0)
			return self::NAME_A . assetParams::CONTAINER_FORMAT_MP4;
		return '';
	}
}
