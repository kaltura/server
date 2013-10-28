<?php
/**
 * @package Core
 * @subpackage model
 */
abstract class LiveEntry extends entry
{
	/* (non-PHPdoc)
	 * @see entry::getLocalThumbFilePath()
	 */
	public function getLocalThumbFilePath($version , $width , $height , $type , $bgcolor ="ffffff" , $crop_provider=null, $quality = 0,
		$src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0, $vid_sec = -1, $vid_slice = 0, $vid_slices = -1, $density = 0, $stripProfiles = false, $flavorId = null, $fileName = null)
	{
		if ($this->getStatus () == entryStatus::DELETED || $this->getModerationStatus () == moderation::MODERATION_STATUS_BLOCK) {
			KalturaLog::log ( "rejected live stream entry - not serving thumbnail" );
			KExternalErrors::dieError ( KExternalErrors::ENTRY_DELETED_MODERATED );
		}
		
		$contentPath = myContentStorage::getFSContentRootPath ();
		$msgPath = $contentPath . "content/templates/entry/thumbnail/live_thumb.jpg";
		return myEntryUtils::resizeEntryImage ( $this, $version, $width, $height, $type, $bgcolor, $crop_provider, $quality, $src_x, $src_y, $src_w, $src_h, $vid_sec, $vid_slice, $vid_slices, $msgPath, $density, $stripProfiles );
	}
	
	public function setOfflineMessage ( $v )	{	$this->putInCustomData ( "offlineMessage" , $v );	}
	public function getOfflineMessage (  )		{	return $this->getFromCustomData( "offlineMessage" );	}

	public function getRecordStatus ()
	{
	    return $this->getFromCustomData("record_status");
	}
	
	public function setRecordStatus ($v)
	{
	    $this->putInCustomData("record_status", $v);
	}

	public function getDvrStatus ()
	{
	    return $this->getFromCustomData("dvr_status");
	}
	
	public function setDvrStatus ($v)
	{
	    $this->putInCustomData("dvr_status", $v);
	}
	
    public function getDvrWindow ()
	{
	    return $this->getFromCustomData("dvr_window");
	}
	
	public function setDvrWindow ($v)
	{
	    $this->putInCustomData("dvr_window", $v);
	}
	
	public function setLiveStreamConfigurations (array $v)
	{
		$this->putInCustomData('live_stream_configurations', $v);
	}
	
	public function getLiveStreamConfigurationByProtocol($protocol, $tag = null)
	{
		$configurations = $this->getLiveStreamConfigurations($tag);
		foreach($configurations as $configuration)
		{
			/* @var $configuration kLiveStreamConfiguration */
			if ($configuration->getProtocol() == $protocol)
				return $configuration;
		}

		return null;
	}
	
	public function getLiveStreamConfigurations($tag = null)
	{
		$configurations = $this->getFromCustomData('live_stream_configurations');
		if($configurations)
			return $configurations;
			
		$configurations = array();
		$mediaServer = $this->getMediaServer();
		if($mediaServer)
		{
			$streamName = $this->getStreamName();
			if(is_null($tag) && $this->getConversionProfileId())
				$tag = 'all';
			
			$manifestUrl = $mediaServer->getManifestUrl() . ($tag ? "ngrp:{$streamName}_{$tag}" : $streamName);
			
			$hlsStreamUrl	.= "/playlist.m3u8";
			$hdsStreamUrl	.= "/playlist.f4m";
			$mpdStreamUrl	.= "/manifest.mpd";
			$slStreamUrl	.= "/Manifest";
			
			if($this->getDvrStatus() == DVRStatus::ENABLED)
			{
				$hlsStreamUrl	.= "?DVR";
				$hdsStreamUrl	.= "?DVR";
				$slStreamUrl	.= "?dvr";
			}
				
			$configuration = new kLiveStreamConfiguration();
			$configuration->setProtocol(PlaybackProtocol::RTMP);
			$configuration->setUrl($mediaServer->getRtmpUrl());
			$configurations[] = $configuration;
			
			$configuration = new kLiveStreamConfiguration();
			$configuration->setProtocol(PlaybackProtocol::HDS);
			$configuration->setUrl($hdsStreamUrl);
			$configurations[] = $configuration;
			
			$configuration = new kLiveStreamConfiguration();
			$configuration->setProtocol(PlaybackProtocol::HLS);
			$configuration->setUrl($hlsStreamUrl);
			$configurations[] = $configuration;
			
			$configuration = new kLiveStreamConfiguration();
			$configuration->setProtocol(PlaybackProtocol::APPLE_HTTP);
			$configuration->setUrl($hlsStreamUrl);
			$configurations[] = $configuration;
			
			$configuration = new kLiveStreamConfiguration();
			$configuration->setProtocol(PlaybackProtocol::SILVER_LIGHT);
			$configuration->setUrl($slStreamUrl);
			$configurations[] = $configuration;
			
			$configuration = new kLiveStreamConfiguration();
			$configuration->setProtocol(PlaybackProtocol::MPEG_DASH);
			$configuration->setUrl($mpdStreamUrl);
			$configurations[] = $configuration;
		}
			
		return $configurations;
	}

	/**
	 * @return MediaServer
	 */
	protected function getMediaServer()
	{
		$mediaServers = $this->getMediaServers();
		if(!count($mediaServers))
			return null;
			
		foreach($mediaServers as $mediaServer)
		{
			/* @var $mediaServer kLiveMediaServer */
			if($mediaServer->getDc() == kDataCenterMgr::getCurrentDcId())
				return $mediaServer->getMediaServer();
		}
		
		$mediaServer = reset($mediaServers);
		return $mediaServer->getMediaServer();
	}
	
	public function setMediaServer($index, $serverId, $hostname)
	{
		// TODO create cache
		
		if($this->isMediaServerRegistered($index, $serverId))
			return;
			
		$servers = $this->getMediaServers();
		$servers[$index] = new kLiveMediaServer($index, $serverId, $hostname);
		
		$this->putInCustomData("mediaServers", $servers);	
	}
	
	protected function isMediaServerRegistered($index, $serverId)
	{
		$servers = $this->getMediaServers();
		if(isset($servers[$index]) && $servers[$index]->getMediaServerId() == $serverId)
			return true;
			
		return false;
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
		$mediaServers = $this->getFromCustomData("mediaServers", null, array());
		// TODO - remove expired cache from $mediaServers
		return $mediaServers;	
	}
}
