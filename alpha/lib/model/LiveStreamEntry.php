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
		$this->setType(EntryType::LIVE_STREAM);
		$this->setStatus(entryStatus::NO_CONTENT);
	}
	
	/* (non-PHPdoc)
	 * @see entry::postInsert($con)
	 */
	public function postInsert(PropelPDO $con = null)
	{
		if(!$this->wasObjectSaved())
			return;
			
		parent::postInsert($con);
	
		if ($this->conversion_profile_id)
			kBusinessConvertDL::decideLiveProfile($this);
	}
	
	public function setEncodingIP1 ( $v )	{	$this->putInCustomData ( "encodingIP1" , $v );	}
	public function getEncodingIP1 (  )		{	return $this->getFromCustomData( "encodingIP1" );	}

	public function setEncodingIP2 ( $v )	{	$this->putInCustomData ( "encodingIP2" , $v );	}
	public function getEncodingIP2 (  )		{	return $this->getFromCustomData( "encodingIP2" );	}

	public function setStreamUsername ( $v )	{	$this->putInCustomData ( "streamUsername" , $v );	}
	public function getStreamUsername (  )		{	return $this->getFromCustomData( "streamUsername" );	}

	public function setStreamPassword ( $v )	{	$this->putInCustomData ( "streamPassword" , $v );	}
	public function getStreamPassword (  )		{	return $this->getFromCustomData( "streamPassword" );	}

	public function setStreamRemoteId ( $v )	{	$this->putInCustomData ( "streamRemoteId" , $v );	}
	public function getStreamRemoteId (  )		{	return $this->getFromCustomData( "streamRemoteId" );	}

	public function setStreamRemoteBackupId ( $v )	{	$this->putInCustomData ( "streamRemoteBackupId" , $v );	}
	public function getStreamRemoteBackupId (  )		{	return $this->getFromCustomData( "streamRemoteBackupId" );	}

	public function setStreamUrl ( $v )	{	$this->putInCustomData ( "streamUrl" , $v );	}
	public function getStreamUrl (  )		{	return $this->getFromCustomData( "streamUrl" );	}
	
	public function setPrimaryBroadcastingUrl ( $v )	{	$this->putInCustomData ( "primaryBroadcastingUrl" , $v );	}
	public function getPrimaryBroadcastingUrl (  )		{	return $this->getFromCustomData( "primaryBroadcastingUrl" );	}
	
	public function setSecondaryBroadcastingUrl ( $v )	{	$this->putInCustomData ( "secondaryBroadcastingUrl" , $v );	}
	public function getSecondaryBroadcastingUrl (  )	{	return $this->getFromCustomData( "secondaryBroadcastingUrl" );	}
	
	public function setStreamName ( $v )	{	$this->putInCustomData ( "streamName" , $v );	}
	public function getStreamName (  )	{	return $this->getFromCustomData( "streamName", null, $this->getId() );	}
	
	public function setStreamBitrates (array $v )	{	$this->putInCustomData ( "streamBitrates" , $v );	}
	public function getStreamBitrates (  )		{	return $this->getFromCustomData( "streamBitrates" );	}
	
	public function setMediaServer($index, $serverId, $hostname)
	{
		$servers = $this->getMediaServers();
		$servers[$index] = new kLiveMediaServer($index, $serverId, $hostname);
		
		$this->putInCustomData("mediaServers", $servers);	
	}
	
	public function unsetMediaServer($index, $serverId)
	{
		$servers = $this->getMediaServers();
		if(isset($servers[$index]) && $servers[$index]->getMediaServerId() == $serverId)
			unset($servers[$index]);
		
		$this->putInCustomData("mediaServers", $servers);	
	}
	
	public function getMediaServers()
	{
		return $this->getFromCustomData("mediaServers", null, array());	
	}
	
	public function getHlsStreamUrl ()
	{
	    return $this->getFromCustomData("hls_stream_url");
	}
	
	public function setHlsStreamUrl ($v)
	{
	    $this->putInCustomData("hls_stream_url", $v);
	}
	
    public function getUrlManager ()
	{
	    return $this->getFromCustomData("url_manager");
	}
	
	public function setUrlManager ($v)
	{
	    $this->putInCustomData("url_manager", $v);
	}
}
