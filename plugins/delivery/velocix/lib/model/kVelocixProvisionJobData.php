<?php

/**
 * @package plugins.velocix
 * @subpackage lib.model
 */
class kVelocixProvisionJobData extends kProvisionJobData
{
	/**
	 * @var array
	 */
	private $provisioningParams;
	
	/**
	 * @var string
	 */
	private $userName;
	
	/**
	 * @var string
	 */
	private $password;
	
	//same constants as in the engine.
	const APPLE_HTTP_URLS = 'applehttp_urls';
	const HDS_URLS = 'hds_urls';
	const SL_URLS = 'sl_urls';
	const PLAYBACK = 'playback';
	const PUBLISH = 'publish';
	
	/**
	 * @return KalturaKeyValueArray $provisioningParams
	 */
	public function getProvisioningParams() {
		return $this->provisioningParams;
	}

	/**
	 * @param KalturaKeyValueArray $provisioningParams
	 */
	public function setProvisioningParams($provisioningParams) {
		$this->provisioningParams = $provisioningParams;
	}
	
	/**
	 * @return string $userName
	 */
	public function getUserName(){
		return $this->userName;
	}
	
	/**
	 * @param string $userName
	 */
	public function setUserName($userName){
		$this->userName = $userName;
	}
	
	/**
	 * @return string $password
	 */
	public function getPassword(){
		return $this->password;
	}
	
	/**
	 * @param string $password
	 */
	public function setPassword($password){
		$this->password = $password;
	}
	
	/* (non-PHPdoc)
	 * @see kProvisionJobData::populateFromPartner()
	 */
	public function populateFromPartner(Partner $partner){
		$liveParams = json_decode($partner->getLiveStreamProvisionParams());
		if (isset($liveParams->velocix))
		{
			$this->userName = $liveParams->velocix->userName;
			$this->password = $liveParams->velocix->password;
		}
	}

	/* (non-PHPdoc)
	 * @see kProvisionJobData::populateEntryFromData()
	 */
	public function populateEntryFromData (entry $entry)
	{
		$configurations = array();
		KalturaLog::debug('provisioningParams: '.print_r($this->provisioningParams,true));
		foreach ($this->provisioningParams as $key => $provisioningParam){
			switch ($key){
				case self::HDS_URLS:
					$urls = unserialize($provisioningParam);
					$configuration = new kLiveStreamConfiguration();
					$configuration->setProtocol(PlaybackProtocol::HDS);
					$configuration->setUrl($urls[self::PLAYBACK]);
					$configuration->setPublishUrl($urls[self::PUBLISH]);
					$configurations[]=$configuration;
					break;
				case self::APPLE_HTTP_URLS:
					$urls = unserialize($provisioningParam);
					$configuration = new kLiveStreamConfiguration();
					$configuration->setProtocol(PlaybackProtocol::APPLE_HTTP);
					$configuration->setUrl($urls[self::PLAYBACK]);
					$configuration->setPublishUrl($urls[self::PUBLISH]);
					$configurations[]=$configuration;
					break;
				case self::SL_URLS:
					$urls = unserialize($provisioningParam);
					$configuration = new kLiveStreamConfiguration();
					$configuration->setProtocol(PlaybackProtocol::SILVER_LIGHT);
					$configuration->setUrl($urls[self::PLAYBACK]);
					$configuration->setPublishUrl($urls[self::PUBLISH]);
					$configurations[]=$configuration;
					break;
			}
		}
		$entry->setLiveStreamConfigurations($configurations);
	}
	
	/* (non-PHPdoc)
	 * @see kProvisionJobData::populateFromEntry()
	 */
	public function populateFromEntry(entry $entry) 
	{
		$this->setStreamName($entry->getStreamName());
		$liveAssets = assetPeer::retrieveByEntryId($entry->getId(),array(assetType::LIVE));
		$playbackProtocols = array();
		$this->provisioningParams = array();
		foreach ($liveAssets as $liveAsset){
			/* @var $liveAsset liveAsset */
			$tags = explode(',', $liveAsset->getTags());
			foreach ($tags as $tag){
				if (isset($this->provisioningParams[$tag])){
					$bitrates = $this->provisioningParams[$tag];
					$bitrates = explode(',', $bitrates);
					$bitrates[] = $liveAsset->getBitrate();
					$this->provisioningParams[$tag] = implode(',', $bitrates);
				}
				else 
					$this->provisioningParams[$tag] = $liveAsset->getBitrate();
			}
		}
	}
}