<?php

abstract class DeliveryProfileLive extends DeliveryProfile {
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kRedirectManifestRenderer';
	}
	
	/**
	 * Method checks whether the URL passed to it as a parameter returns a response.
	 * @param string $url
	 * @return string
	 */
	protected function urlExists ($url, array $contentTypeToReturn, $range = null)
	{
		if (is_null($url))
			return false;
		if (!function_exists('curl_init'))
		{
			KalturaLog::err('Unable to use util when php curl is not enabled');
			return false;
		}
		KalturaLog::log("Checking URL [$url] with range [$range]");
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		if (!is_null($range))
		{
			curl_setopt($ch, CURLOPT_RANGE, $range);
		}
		$data = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		curl_close($ch);
	
		$contentTypeToCheck = strstr($contentType, ";", true);
		if(!$contentTypeToCheck)
			$contentTypeToCheck = $contentType;
		if($data && $httpcode>=200 && $httpcode<300)
		{
			$contentTypeToCheck = strtolower( trim( $contentTypeToCheck ) );
			foreach ( $contentTypeToReturn as $cttr )
			{
				if ( $contentTypeToCheck === strtolower( trim( $cttr ) ) )
				{
					return $data;
				}
			}

			return true;
		}
		else
			return false;
	}
	
	/**
	 * Function check if URL provided is a valid one if not returns fixed url with the parent url relative path
	 * @param string $urlToCheck
	 * @param string $parentURL
	 * @return fixed url path
	 */
	protected function checkIfValidUrl($urlToCheck, $parentURL)
	{
		$urlToCheck = trim($urlToCheck);
		if (strpos($urlToCheck, '://') === false)
		{
			$urlToCheck = dirname($parentURL) . '/' . $urlToCheck;
		}
	
		return $urlToCheck;
	}
	
	public final function serve(kLiveStreamConfiguration $liveStreamConfig) 
	{
		return $this->doServe($liveStreamConfig);
	}
	
	public function doServe(kLiveStreamConfiguration $liveStreamConfig) 
	{
		$flavors = array();
		$baseUrl = $liveStreamConfig->getUrl();
		$this->finalizeUrls($baseUrl, $flavors);
	
		$flavors[] = $this->getFlavorAssetInfo('', $baseUrl);		// passing the url as urlPrefix so that only the path will be tokenized
		$renderer = $this->getRenderer($flavors);
		return $renderer;
	}
	
	public function finalizeUrls(&$baseUrl, &$flavorsUrls)
	{
		if($this->params->getEdgeServerIds())
			$baseUrl = $this->getDeliveryServerNodeUrl($baseUrl);
	}
	
	public function isLive ($url) {
		$url = $this->getTokenizedUrl($url);
		return $this->checkIsLive($url);
	}
	
	protected function getTokenizedUrl($url){
		$urlPath = parse_url($url, PHP_URL_PATH);
		if (!$urlPath || substr($url, -strlen($urlPath)) != $urlPath)
			return $url;
		$urlPrefix = substr($url, 0, -strlen($urlPath));
		$tokenizer = $this->getTokenizer();
		if ($tokenizer)
			return $urlPrefix.$tokenizer->tokenizeSingleUrl($urlPath);
		return $url;
	}
	
	protected function checkIsLive($url) {
		throw new Exception('Status cannot be determined for live stream protocol. Delivery Profile ID: '.$this->getId());
	}
	
	public function getDeliveryServerNodeUrl($url)
	{
		if(!$url)
        	return null;
		
		$deliveryNodeIds = $this->params->getEdgeServerIds();
		$deliveryNodes = ServerNodePeer::retrieveOrderedServerNodesArrayByPKs($deliveryNodeIds);
		
		if(!count($deliveryNodes))
		{
		        KalturaLog::debug("No active delivery nodes found to handle [$url]");
		        return null;
		}
		
		$deliveryNode = array_shift($deliveryNodes);
		$url = $deliveryNode->buildPlaybackUrl($url);
		
		if(count($deliveryNodes))
		        $this->params->setEdgeServerIds(array_diff($deliveryNodeIds, array($deliveryNode->getId())));
		
		return $url;
	}
}

