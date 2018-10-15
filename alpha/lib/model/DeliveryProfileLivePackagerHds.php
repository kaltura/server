<?php

class DeliveryProfileLivePackagerHds extends DeliveryProfileLiveHds {
	
	protected function getHttpUrl($serverNode)
	{
		$httpUrl = $this->getLivePackagerUrl($serverNode);
		$httpUrl .= "manifest";
		
		foreach($this->getDynamicAttributes()->getFlavorParamIds() as $flavorId)
		{
			$httpUrl .= "-s$flavorId";
		}
		
		$httpUrl .= ".f4m";
		
		KalturaLog::debug("Live Stream url [$httpUrl]");
		return $httpUrl;
	}
}

