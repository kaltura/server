<?php
/**
 * @package Core
 * @subpackage model
 */
class LiveStreamEntry extends LiveEntry
{
	const PRIMARY_BROADCASTING_URL = 'primaryBroadcastingUrl';
	const SECONDARY_BROADCASTING_URL = 'secondaryBroadcastingUrl';
	const PRIMARY_RTMPS_BROADCASTING_URL = 'primaryRtmpsBroadcastingUrl';
	const SECONDARY_RTMPS_BROADCASTING_URL = 'secondaryRtmpsBroadcastingUrl';
	const PRIMARY_RTSP_BROADCASTING_URL = 'primaryRtspBroadcastingUrl';
	const SECONDARY_RTSP_BROADCASTING_URL = 'secondaryRtspBroadcastingUrl';
	const PRIMARY_SRT_BROADCASTING_URL = 'primarySrtBroadcastingUrl';
	const SECONDARY_SRT_BROADCASTING_URL = 'secondarySrtBroadcastingUrl';
	const PRIMARY_SRT_STREAM_ID = 'primarySrtStreamId';
	const SECONDARY_SRT_STREAM_ID = 'secondarySrtStreamId';
	const LIVE_STATUS_CONDITIONAL_CACHE_EXPIRY = 10;
	
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

	public function setStreamPassword ( $v )	{	$this->putInCustomData ( "streamPassword" , $v );	}
	public function getStreamPassword (  )		{	return $this->getFromCustomData( "streamPassword" );	}

	public function setSrtPass ( $v )	{	$this->putInCustomData ( "srtPass" , $v );	}
	public function getSrtPass (  )		{	return $this->getFromCustomData( "srtPass" );	}

	public function setStreamRemoteId ( $v )	{	$this->putInCustomData ( "streamRemoteId" , $v );	}
	public function getStreamRemoteId (  )		{	return $this->getFromCustomData( "streamRemoteId" );	}

	public function setStreamRemoteBackupId ( $v )	{	$this->putInCustomData ( "streamRemoteBackupId" , $v );	}
	public function getStreamRemoteBackupId (  )		{	return $this->getFromCustomData( "streamRemoteBackupId" );	}

	public function setStreamUrl ( $v )	{	$this->putInCustomData ( "streamUrl" , $v );	}
	public function getStreamUrl (  )		{	return $this->getFromCustomData( "streamUrl" );	}
	
	public function setPrimaryBroadcastingUrl ( $v )	{	$this->putInCustomData ( self::PRIMARY_BROADCASTING_URL , $v );	}
	public function getPrimaryBroadcastingUrl (  )
	{
		return $this->getDynamicBroadcastUrl(self::PRIMARY_BROADCASTING_URL, 'getPrimaryBroadcastUrl', kBroadcastUrlManager::PROTOCOL_RTMP);
	}

	public function setSecondaryBroadcastingUrl ( $v )	{	$this->putInCustomData ( self::SECONDARY_BROADCASTING_URL , $v );	}
	public function getSecondaryBroadcastingUrl (  )
	{
		return $this->getDynamicBroadcastUrl(self::SECONDARY_BROADCASTING_URL, 'getSecondaryBroadcastUrl', kBroadcastUrlManager::PROTOCOL_RTMP);
	}

	public function setPrimarySecuredBroadcastingUrl ( $v )	{	$this->putInCustomData ( self::PRIMARY_RTMPS_BROADCASTING_URL , $v );	}
	public function getPrimarySecuredBroadcastingUrl (  )
	{
		return $this->getDynamicBroadcastUrl(self::PRIMARY_RTMPS_BROADCASTING_URL, 'getPrimaryBroadcastUrl', kBroadcastUrlManager::PROTOCOL_RTMPS);
	}

	public function setSecondarySecuredBroadcastingUrl ( $v )	{	$this->putInCustomData ( LiveStreamEntry::SECONDARY_RTMPS_BROADCASTING_URL , $v );	}
	public function getSecondarySecuredBroadcastingUrl (  )
	{
		return $this->getDynamicBroadcastUrl(self::SECONDARY_RTMPS_BROADCASTING_URL, 'getSecondaryBroadcastUrl', kBroadcastUrlManager::PROTOCOL_RTMPS);
	}
	
	public function setPrimaryRtspBroadcastingUrl ( $v )	{	$this->putInCustomData ( self::PRIMARY_RTSP_BROADCASTING_URL , $v );	}
	public function getPrimaryRtspBroadcastingUrl (  )
	{
		return $this->getDynamicBroadcastUrl(self::PRIMARY_RTSP_BROADCASTING_URL, 'getPrimaryBroadcastUrl', kBroadcastUrlManager::PROTOCOL_RTSP);
	}
	
	public function setSecondaryRtspBroadcastingUrl ( $v )	{	$this->putInCustomData ( self::SECONDARY_RTSP_BROADCASTING_URL , $v );	}
	public function getSecondaryRtspBroadcastingUrl (  )
	{
		return $this->getDynamicBroadcastUrl(self::SECONDARY_RTSP_BROADCASTING_URL, 'getSecondaryBroadcastUrl', kBroadcastUrlManager::PROTOCOL_RTSP);
	}

	public function setPrimarySrtBroadcastingUrl ( $v )
	{
		$this->putInCustomData ( self::PRIMARY_SRT_BROADCASTING_URL , $v );
	}
	public function getPrimarySrtBroadcastingUrl (  )
	{
		return $this->getDynamicBroadcastUrl(self::PRIMARY_SRT_BROADCASTING_URL, 'getPrimaryBroadcastUrl', kBroadcastUrlManager::PROTOCOL_SRT);
	}

	public function setSecondarySrtBroadcastingUrl ( $v )
	{
		$this->putInCustomData ( self::SECONDARY_SRT_BROADCASTING_URL , $v );
	}
	public function getSecondarySrtBroadcastingUrl (  )
	{
		return $this->getDynamicBroadcastUrl(self::SECONDARY_SRT_BROADCASTING_URL, 'getSecondaryBroadcastUrl', kBroadcastUrlManager::PROTOCOL_SRT);
	}

	public function setPrimarySrtStreamId ( $v )
	{
		$this->putInCustomData ( self::PRIMARY_SRT_STREAM_ID , $v );
	}
	public function getPrimarySrtStreamId (  )
	{
		return $this->getSrtStreamId(self::PRIMARY_SRT_STREAM_ID, kBroadcastUrlManager::PRIMARY_MEDIA_SERVER_INDEX);
	}

	public function setSecondarySrtStreamId ( $v )
	{
		$this->putInCustomData ( self::SECONDARY_SRT_STREAM_ID , $v );
	}
	public function getSecondarySrtStreamId (  )
	{
		return $this->getSrtStreamId(self::SECONDARY_SRT_STREAM_ID, kBroadcastUrlManager::SECONDARY_MEDIA_SERVER_INDEX);
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

	protected function getSrtStreamId($customDataParam, $sessionType)
	{
		$streamId = $this->getFromCustomData($customDataParam);
		if($streamId)
		{
			return $streamId;
		}

		$manager = kBroadcastUrlManager::getInstance($this->getPartnerId());
		$streamId = $manager->createSrtStreamId($this, $sessionType);
		return $streamId;
	}

	public function getLiveStreamConfigurations($protocol = null, $tag = null, $currentDcOnly = false, array $flavorParamsIds = array(), $format = null)
	{
		if(!$protocol)
		{
			$protocol = requestUtils::getProtocol();
		}
		$configurations =  parent::getLiveStreamConfigurations($protocol, $tag, $currentDcOnly, $flavorParamsIds, $format);
		if($format == PlaybackProtocol::APPLE_HTTP && !in_array($this->getSource(), self::$kalturaLiveSourceTypes) && $this->getHlsStreamUrl())
		{
			$hlsLiveStreamConfig = new kLiveStreamConfiguration();
			$hlsLiveStreamConfig->setUrl($this->getHlsStreamUrl());
			$hlsLiveStreamConfig->setProtocol(PlaybackProtocol::APPLE_HTTP);
			$configurations[] = $hlsLiveStreamConfig;
		}

		return $configurations;
	}

	public function copyTemplate($template, $copyPartnerId = false)
	{
		if ($template instanceof LiveStreamEntry)
		{
			$this->setExplicitLive($template->getExplicitLive());
		}
		return parent::copyTemplate($template, $copyPartnerId);
	}

	public function setIsSipEnabled ( $v )	{	$this->putInCustomData ( "isSipEnabled" , $v );	}
	public function getIsSipEnabled (  )	{	return $this->getFromCustomData( "isSipEnabled", null, false );	}
	public function setSipRoomId ( $v )	{	$this->putInCustomData ( "sipRoomId" , $v );	}
	public function getSipRoomId(  )	{	return (int) $this->getFromCustomData( "sipRoomId", null, 0 );	}
	public function setPrimaryAdpId ( $v )	{	$this->putInCustomData ( "primaryAdpId" , $v );	}
	public function getPrimaryAdpId(  )	{	return $this->getFromCustomData( "primaryAdpId", null, false );	}
	public function setSecondaryAdpId( $v )	{	$this->putInCustomData ( "secondaryAdpId" , $v );	}
	public function getSecondaryAdpId(  )	{	return $this->getFromCustomData( "secondaryAdpId", null, false );	}
	public function setSipToken ( $v )  {	$this->putInCustomData ( "sipToken" , $v );	}
	public function getSipToken( )  { return $this->getFromCustomData( "sipToken" ); }
	public function setSipSourceType ( $v )	{	$this->putInCustomData ( "sipSourceType" , $v );	}
	public function getSipSourceType (  )	{	return $this->getFromCustomData( "sipSourceType" );	}
	public function setSipDualStreamEntryId ( $v )	{	$this->putInCustomData ( "sipDualStreamEntryId" , $v );	}
	public function getSipDualStreamEntryId (  )	{	return $this->getFromCustomData( "sipDualStreamEntryId" );	}

	/**
	 * generate a random 8-character string as stream password
	 * @return string - streaming password
	 */
	public static function generateStreamPassword()
	{
		$password = sha1(md5(uniqid(rand(), true)));
		return substr($password, rand(0, strlen($password) - 8), 8);
	}

	public function copyInto($copyObj, $deepCopy = false)
	{
		parent::copyInto($copyObj, $deepCopy);
		$copyObj->setStreamPassword(self::generateStreamPassword()); //password should be re-generated on copy
	}

	public function setLiveStatusCache()
	{
		if (!in_array($this->getSource(), self::$kalturaLiveSourceTypes))
		{
			KalturaResponseCacher::setConditionalCacheExpiry(self::LIVE_STATUS_CONDITIONAL_CACHE_EXPIRY);
		}
		
		$simuliveCondCacheTime = kSimuliveUtils::getIsLiveCacheTime($this);
		if ($simuliveCondCacheTime)
		{
			KalturaResponseCacher::setConditionalCacheExpiry($simuliveCondCacheTime);
		}
	}
}
