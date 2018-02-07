<?php
/**
 * @package Core
 * @subpackage model
 */
class LiveStreamEntry extends LiveEntry
{
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(entryType::LIVE_STREAM);
		$this->setStatus(entryStatus::NO_CONTENT);
	}
	
	public function setEncodingIP1 ( $v )	{	$this->putInCustomData ( "encodingIP1" , $v );	}
	public function getEncodingIP1 (  )		{	return $this->getFromCustomData( "encodingIP1" );	}

	public function setEncodingIP2 ( $v )	{	$this->putInCustomData ( "encodingIP2" , $v );	}
	public function getEncodingIP2 (  )		{	return $this->getFromCustomData( "encodingIP2" );	}

	public function setStreamUsername ( $v )	{	$this->putInCustomData ( "streamUsername" , $v );	}
	public function getStreamUsername (  )		{	return $this->getFromCustomData( "streamUsername" );	}

	public function setStreamRemoteId ( $v )	{	$this->putInCustomData ( "streamRemoteId" , $v );	}
	public function getStreamRemoteId (  )		{	return $this->getFromCustomData( "streamRemoteId" );	}

	public function setStreamRemoteBackupId ( $v )	{	$this->putInCustomData ( "streamRemoteBackupId" , $v );	}
	public function getStreamRemoteBackupId (  )		{	return $this->getFromCustomData( "streamRemoteBackupId" );	}

	public function setStreamUrl ( $v )	{	$this->putInCustomData ( "streamUrl" , $v );	}
	public function getStreamUrl (  )		{	return $this->getFromCustomData( "streamUrl" );	}
	
	public function setPrimaryBroadcastingUrl ( $v )	{	$this->putInCustomData ( "primaryBroadcastingUrl" , $v );	}
	public function getPrimaryBroadcastingUrl (  )
	{
		return $this->getDynamicBroadcastUrl('primaryBroadcastingUrl', 'getPrimaryBroadcastUrl', kBroadcastUrlManager::PROTOCOL_RTMP);
	}
	
	public function setSecondaryBroadcastingUrl ( $v )	{	$this->putInCustomData ( "secondaryBroadcastingUrl" , $v );	}
	public function getSecondaryBroadcastingUrl (  )
	{
		return $this->getDynamicBroadcastUrl('secondaryBroadcastingUrl', 'getSecondaryBroadcastUrl', kBroadcastUrlManager::PROTOCOL_RTMP);
	}
	
	public function setPrimaryRtspBroadcastingUrl ( $v )	{	$this->putInCustomData ( "primaryRtspBroadcastingUrl" , $v );	}
	public function getPrimaryRtspBroadcastingUrl (  )
	{
		return $this->getDynamicBroadcastUrl('primaryRtspBroadcastingUrl', 'getPrimaryBroadcastUrl', kBroadcastUrlManager::PROTOCOL_RTSP);
	}
	
	public function setSecondaryRtspBroadcastingUrl ( $v )	{	$this->putInCustomData ( "secondaryRtspBroadcastingUrl" , $v );	}
	public function getSecondaryRtspBroadcastingUrl (  )
	{
		return $this->getDynamicBroadcastUrl('secondaryRtspBroadcastingUrl', 'getSecondaryBroadcastUrl', kBroadcastUrlManager::PROTOCOL_RTSP);
	}
	
	public function setPrimaryServerNodeId ( $v )	{	$this->putInCustomData ( "primaryServerNodeId" , $v );	}
	public function getPrimaryServerNodeId (  )	{	return $this->getFromCustomData( "primaryServerNodeId", null, null );	}
	
	public function getHlsStreamUrl ($protocol = null)
	{
		if(!$protocol)
			$protocol = requestUtils::getRequestProtocol();
		
		$hlsStreamUrl = $this->getFromCustomData("hls_stream_url");
		//If request protocol is https modify hls stream url to return https to avoid mixed content browser errors
		if($hlsStreamUrl && kString::beginsWith($hlsStreamUrl, 'http://') && $protocol == 'https')
		{
			$hlsStreamUrl =  preg_replace('/^http/', 'https' , $hlsStreamUrl);
		}
		
	    return $hlsStreamUrl;
	}
	
	public function setHlsStreamUrl ($v)
	{
	    $this->putInCustomData("hls_stream_url", $v);
	}

	private function getDynamicBroadcastUrl($customDataParam, $functionName, $protocol)
	{
		$url = $this->getFromCustomData($customDataParam);
		if($url)
			return $url;

		$manager = kBroadcastUrlManager::getInstance($this->getPartnerId());
		$url = $manager->$functionName($this, $protocol);
		return $url;
	}
}
