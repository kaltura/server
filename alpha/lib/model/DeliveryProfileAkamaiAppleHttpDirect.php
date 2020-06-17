<?php

class DeliveryProfileAkamaiAppleHttpDirect extends DeliveryProfileAkamaiAppleHttp {
	
	protected function doGetFlavorAssetUrl(asset $flavorAsset)
	{
		if (strpos($flavorAsset->getTags(), flavorParams::TAG_APPLEMBR) === FALSE)
		{
			$url = $this->getBaseUrl($flavorAsset);
			if($this->params->getClipTo())
			{
				$url = self::insertAfter($url, 'entryId', 'clipTo', $this->params->getClipTo());
			}
			// we use index_0_av.m3u8 instead of master.m3u8 as temporary solution to overcome
			// an extra "redirection" done on the part of akamai.
			// the auto created master.m3u8 file contains a single item playlist to the index_0_av.m3u8 file
			// this extra "redirection" fails
			return $this->formatHdIos($url);
		}
		else {
			KalturaLog::err("@_!! WE DO GET TO THE ELSE");
			return parent::doGetFlavorAssetUrl($flavorAsset);
		}
	}
	
	protected function getUrlPrefix()
	{
		$parsedUrl = parse_url($this->getUrl());
		return $parsedUrl["scheme"] . "://" . $parsedUrl["host"];
	}
	
	protected function formatHdIos($path) {
		$urlpath = ltrim(parse_url($this->getUrl(), PHP_URL_PATH),"/");
	
		$urlPrefix = '/i/' . $urlpath;
		$urlSuffix = str_replace('\\', '/', $path)."/index_0_av.m3u8";
	
		return $urlPrefix.ltrim($urlSuffix, '/');
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$path = parent::doGetFileSyncUrl($fileSync);
		return $this->formatHdIos($path);
	}
}

