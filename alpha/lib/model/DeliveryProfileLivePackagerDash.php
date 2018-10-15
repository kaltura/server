<?php

class DeliveryProfileLivePackagerDash extends DeliveryProfileLiveDash
{
	function __construct()
	{
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kRedirectManifestRenderer';
	}
	
	protected function getHttpUrl($serverNode)
	{
		$httpUrl = $this->getLivePackagerUrl($serverNode);
		$httpUrl .= "manifest";
		
		foreach($this->getDynamicAttributes()->getFlavorParamIds() as $flavorId)
		{
			$httpUrl .= "-s$flavorId";
		}
		
		$httpUrl .= ".mpd";
		
		KalturaLog::debug("Live Stream url [$httpUrl]");
		return $httpUrl;
	}
}

