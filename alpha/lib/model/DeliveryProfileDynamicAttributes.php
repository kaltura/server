<?php

/**
 * This class centralizes all the delivery attributes that are specific for a given request
 * and not general for the delivery definition.
 * For example- when a request is passed from the Playmanifest - all parameters should be passed through 
 * this data transfer object 
 */
class DeliveryProfileDynamicAttributes {
	
	/**
	 * List of delivery profiles ids which should be enfroced due to an access control action
	 * @var array
	 */
	protected $deliveryProfileIds = null;
	
	/**
	 * Defines whether the list of delivery profiles should be whitelist or blacklisted 
	 * @var bool
	 */
	protected $isDeliveryProfilesBlockedList = null;
	
	/**
	 * @var string
	 */
	protected $format;
	
	/**
	 * @var string
	 */
	protected $extension = null;
	
	/**
	 * @var string
	 */
	protected $containerFormat = null;
	
	/**
	 * @var int
	 */
	protected $seekFromTime = null;
	
	/**
	 * @var int
	 */
	protected $clipTo = null;
	
	/**
	 * @var float
	 */
	protected $playbackRate = null;
	
	/**
	 * @var int
	 */
	protected $storageId = null;
	
	/**
	 * @var string
	 */
	protected $entryId = null;
	
	/**
	 * may contain several fallbacks options, each one with a set of tags
	 * @var array
	 */
	protected $tags;
	
	/**
	 * @var array
	 */
	protected $flavorAssets = array();

	/**
	 * @var array
	 */
	protected $flavorParamIds = array();
	
	/**
	 * @var array
	 */
	protected $remoteFileSyncs;
	
	/**
	 * TODO Remove me???
	 * @var FileSync
	 */
	protected $manifestFileSync = null;
	
	/**
	 * @var int
	 */
	protected $preferredBitrate = null;
	
	/**
	 * @var string
	 */
	protected $responseFormat;
	
	/**
	 * @var string
	 */
	protected $mediaProtocol = infraRequestUtils::PROTOCOL_HTTP;
	
	/**
	 * @var boolean
	 */
	protected $usePlayServer = false;
	
	/**
	 * @var string
	 */
	protected $playerConfig = null;
	
	/**
	 * @var int
	 */
	protected $uiConfId = null;
	
	/**
	 * @var string
	 */
	protected $urlParams = '';
	
	/**
	 * List of edge server ids content should b server from
	 * @var array
	 */
	protected $edgeServerIds;
	
	/**
	 * @var bool
	 */
	protected $addThumbnailExtension;
	
	/**
	 * @var bool
	 */
	protected $serveVodFromLive;
	
	/**
	 * @var string
	 */
	protected $serveLiveAsVodEntryId;

	/**
	 * @var string
	 */
	protected $sessionId;

	/**
	 * request a specific delivery profile id
	 * @var int
	 */
	protected $deliveryProfileId = null;
	
	/**
	 * List of flavor params ids swhich should be enfroced due to an access control action
	 * @var array
	 */
	protected $aclFlavorParamsIds = null;
	
	/**
	 * Defines whether the list of flavor params ids should be whitelist or blacklisted 
	 * @var bool
	 */
	protected $isAclFlavorParamsIdsBlockedList = null;

	/**
	 * @var string
	 */
	protected $sequence = null;

	/**
	 * @var bool
	 */
	protected  $hasValidSequence = false;
	
	/**
	 * @var string
	 */
	protected  $defaultAudioLanguage = null;

	/**
	 * @return the $addThumbnailExtension
	 */
	public function getAddThumbnailExtension() {
		return $this->addThumbnailExtension;
	}

	/**
	 * @param bool $addThumbnailExtension
	 */
	public function setAddThumbnailExtension($addThumbnailExtension) {
		$this->addThumbnailExtension = $addThumbnailExtension;
	}

	/**
	 * @return the $deliveryProfileIds
	 */
	public function getDeliveryProfileIds() {
		return $this->deliveryProfileIds;
	}

	/**
	 * @return the $isDeliveryProfilesBlockedList
	 */
	public function getIsDeliveryProfilesBlockedList() {
		return $this->isDeliveryProfilesBlockedList;
	}
		
	/**
	 * @return the $format
	 */
	public function getFormat() {
		return $this->format;
	}

	/**
	 * @return the $extension
	 */
	public function getFileExtension() {
		return $this->extension;
	}

	/**
	 * @return the $containerFormat
	 */
	public function getContainerFormat() {
		return $this->containerFormat;
	}

	/**
	 * @return the $seekFromTime
	 */
	public function getSeekFromTime() {
		return $this->seekFromTime;
	}

	/**
	 * @return the $clipTo
	 */
	public function getClipTo() {
		return $this->clipTo;
	}
	
	/**
	 * @return the $playbackRate
	 */
	public function getPlaybackRate() {
		return $this->playbackRate;
	}

	/**
	 * @return the $storageId
	 */
	public function getStorageId() {
		return $this->storageId;
	}

	/**
	 * @return the $entryId
	 */
	public function getEntryId() {
		return $this->entryId;
	}
	
	/**
	 * @return the $entry
	 */
	public function getEntry()
	{
		return entryPeer::retrieveByPK($this->getEntryId());
	}

	/**
	 * @return the $flavorAssets
	 */
	public function getFlavorAssets() {
		return $this->flavorAssets;
	}

	/**
	 * @return array $flavorParamIds
	 */
	public function getFlavorParamIds() {
		return $this->flavorParamIds;
	}

	/**
	 * @return the $remoteFileSyncs
	 */
	public function getRemoteFileSyncs() {
		return $this->remoteFileSyncs;
	}

	/**
	 * @return the $manifestFileSync
	 */
	public function getManifestFileSync() {
		return $this->manifestFileSync;
	}

	/**
	 * @return the $preferredBitrate
	 */
	public function getPreferredBitrate() {
		return $this->preferredBitrate;
	}

	/**
	 * @return int $deliveryProfileId
	 */
	public function getDeliveryProfileId() {
		return $this->deliveryProfileId;
	}

	/**
	 * @param $deliveryProfileId
	 */
	public function setDeliveryProfileId($deliveryProfileId) {
		$this->deliveryProfileId = $deliveryProfileId;
	}

	/**
	 * @param string $deliveryProfileIds
	 * @param bool $isBlockedList
	 */
	public function setDeliveryProfileIds($deliveryProfileIds, $isBlockedList) {
		$this->deliveryProfileIds = $deliveryProfileIds;
		$this->isDeliveryProfilesBlockedList = $isBlockedList;
	}

	/**
	 * @param string $format
	 */
	public function setFormat($format) {
		$this->format = $format;
	}

	/**
	 * @param string $extension
	 */
	public function setFileExtension($extension) {
		$this->extension = $extension;
	}

	/**
	 * @param string $containerFormat
	 */
	public function setContainerFormat($containerFormat) {
		$this->containerFormat = $containerFormat;
	}

	/**
	 * @param number $seekFromTime
	 */
	public function setSeekFromTime($seekFromTime) {
		$this->seekFromTime = $seekFromTime;
	}

	/**
	 * @param number $clipTo
	 */
	public function setClipTo($clipTo) {
		$this->clipTo = $clipTo;
	}

	/**
	 * @param number $playbackRate
	 */
	public function setPlaybackRate($playbackRate) {
		$this->playbackRate = $playbackRate;
	}
	
	/**
	 * @param number $storageId
	 */
	public function setStorageId($storageId) {
		$this->storageId = $storageId;
	}

	/**
	 * @param string $entryId
	 */
	public function setEntryId($entryId) {
		$this->entryId = $entryId;
	}

	/**
	 * @param multitype: $flavorAssets
	 */
	public function setFlavorAssets($flavorAssets) {
		$this->flavorAssets = $flavorAssets;
	}

	/**
	 * @param multitype: $flavorAssets
	 */
	public function setFlavorParamIds($flavorParamIds) {
		$this->flavorParamIds = $flavorParamIds;
	}

	/**
	 * @param multitype: $remoteFileSyncs
	 */
	public function setRemoteFileSyncs($remoteFileSyncs) {
		$this->remoteFileSyncs = $remoteFileSyncs;
	}

	/**
	 * @param FileSync $manifestFileSync
	 */
	public function setManifestFileSync($manifestFileSync) {
		$this->manifestFileSync = $manifestFileSync;
	}

	/**
	 * @param number $preferredBitrate
	 */
	public function setPreferredBitrate($preferredBitrate) {
		$this->preferredBitrate = $preferredBitrate;
	}
	
	/**
	 * @return the $responseFormat
	 */
	public function getResponseFormat() {
		return $this->responseFormat;
	}

	/**
	 * @param string $responseFormat
	 */
	public function setResponseFormat($responseFormat) {
		$this->responseFormat = $responseFormat;
	}
	
	/**
	 * @return array $tags
	 */
	public function getTags() {
		return $this->tags;
	}

	/**
	 * @param multitype: $tags
	 */
	public function setTags($tags) {
		$this->tags = $tags;
	}
	
	/**
	 * @return the $mediaProtocol
	 */
	public function getMediaProtocol() {
		return $this->mediaProtocol;
	}

	/**
	 * @param string $mediaProtocol
	 */
	public function setMediaProtocol($mediaProtocol) {
		$this->mediaProtocol = $mediaProtocol;
	}

	/**
	 * @return the $usePlayServer
	 */
	public function getUsePlayServer()
	{
		return $this->usePlayServer;
	}

	/**
	 * @return array $playerConfig
	 */
	public function getPlayerConfig()
	{
		return $this->playerConfig;
	}

	/**
	 * @param boolean $usePlayServer
	 */
	public function setUsePlayServer($usePlayServer)
	{
		$this->usePlayServer = $usePlayServer;
	}

	/**
	 * @param string $playerConfig
	 */
	public function setPlayerConfig($playerConfig)
	{
		if($this->usePlayServer && !$this->isPlayerConfigValid($playerConfig))
			return;
		
		$this->playerConfig = $playerConfig;
	}
	
	private function isPlayerConfigValid($playerConfig)
	{
		$playConfigJson = json_decode($playerConfig);
		
		if(json_last_error() != JSON_ERROR_NONE)
		{
			KalturaLog::debug("playerConfig provided is not a json object, data will not be forward to playServer [$playerConfig]");
			return false;
		}
		
		if(isset($playConfigJson->sessionId) && is_int($playConfigJson->sessionId))
		{
			KalturaLog::debug("Integer sessionId value provided in player config, data will not be forward to playServer [$playerConfig]");
			return false;
		}
		
		return true;
	}
	
	/**
	 * @return the uiConfId
	 */
	public function getUiConfId()
	{
		return $this->uiConfId;
	}
	
	/**
	 * @param string $uiConfId
	 */
	public function setUiConfId($uiConfId)
	{
		$this->uiConfId = $uiConfId;
	}

	/**
	 * @param string $urlParamsString
	 */
	public function setUrlParams($urlParamsString)
	{
		$this->urlParams = $urlParamsString;
	}
	
	/**
	 * @return the urlParams
	 */
	public function getUrlParams()
	{
		return $this->urlParams;
	}
	
	/**
	 * @return array edge server ids
	 */
	public function getEdgeServerIds()
	{
		return $this->edgeServerIds;
	}
	
	/**
	 * @param array edge server ids
	 */
	public function setEdgeServerIds($edgeServerIds)
	{
		$this->edgeServerIds = $edgeServerIds;
	}
	
	public function setServeVodFromLive($serveVodFromLive)
	{
		$this->serveVodFromLive = $serveVodFromLive;
	}
	
	public function getServeVodFromLive()
	{
		return $this->serveVodFromLive;
	}
	
	public function setServeLiveAsVodEntryId($serveLiveAsVodEntryId)
	{
		$this->serveLiveAsVodEntryId = $serveLiveAsVodEntryId;
	}
	
	public function getServeLiveAsVodEntryId()
	{
		return $this->serveLiveAsVodEntryId;
	}

	/**
	 * @return the $sessionId
	 */
	public function getSessionId() {
		return $this->sessionId;
	}

	/**
	 * @param $sessionId
	 */
	public function setSessionId($sessionId) {
		$this->sessionId = $sessionId;
	}
	
	/**
	 * @param array $aclFlavorParamsIds
	 * @param bool $isAclFlavorParamsIdsBlockedList
	 */
	public function setAclFlavorParamsIds($aclFlavorParamsIds, $isAclFlavorParamsIdsBlockedList)
	{
		$this->aclFlavorParamsIds = $aclFlavorParamsIds;
		$this->isAclFlavorParamsIdsBlockedList = $isAclFlavorParamsIdsBlockedList;
	
	}
	
	/**
	 * @return the $aclFlavorParamsIds
	 */
	public function getAclFlavorParamsIds() {
		return $this->aclFlavorParamsIds;
	}
	
	/**
	 * @return the $isAclFlavorParamsIdsBlockedList
	 */
	public function getIsAclFlavorParamsIdsBlockedList() {
		return $this->isAclFlavorParamsIdsBlockedList;
	}

	/**
	 * @param array<asset|assetParams> $flavors
	 * @return array
	 */
	public function filterFlavorsByTags($flavors)
	{
		foreach ($this->tags as $tagsFallback)
		{
			$curFlavors = array();
				
			foreach ($flavors as $flavor)
			{
				foreach ($tagsFallback as $tagOption)
				{
					if (!$flavor->hasTag($tagOption))
						continue;
					$curFlavors[] = $flavor;
					break;
				}
			}
				
			if ($curFlavors)
				return $curFlavors;
		}
		return array();
	}

	/**
	 * @return string
	 */
	public function getSequence()
	{
		return $this->sequence;
	}

	/**
	 * @param string $sequence
	 */
	public function setSequence($sequence)
	{
		$this->sequence = $sequence;
	}

	/**
	 * @return boolean
	 */
	public function getHasValidSequence()
	{
		return $this->hasValidSequence;
	}

	/**
	 * @param boolean $hasValidSequence
	 */
	public function setHasValidSequence($hasValidSequence)
	{
		$this->hasValidSequence = $hasValidSequence;
	}

	/**
	 * @return string
	 */
	public function getDefaultAudioLanguage()
	{
		return $this->defaultAudioLanguage;
	}

	/**
	 * @param string $defaultAudioLanguage
	 */
	public function setDefaultAudioLanguage($defaultAudioLanguage)
	{
		$this->defaultAudioLanguage = $defaultAudioLanguage;
	}

	/**
	 * 
	 * @param int $storageId
	 * @param string $entryId
	 * @param PlaybackProtocol $format
	 * @param string $mediaProtocol
	 * @return DeliveryProfileDynamicAttributes
	 */
	public static function init($storageId, $entryId, $format = PlaybackProtocol::HTTP, $mediaProtocol = null)
	{
		$instance = new DeliveryProfileDynamicAttributes();
		$instance->setStorageId($storageId);
		$instance->setEntryId($entryId);
		$instance->setFormat($format);
		$instance->setMediaProtocol($mediaProtocol);
		
		return $instance;
	}
	
	public function cloneAttributes(DeliveryProfileDynamicAttributes $newObj) {
		$this->deliveryProfileIds = $newObj->getDeliveryProfileIds();
		$this->isDeliveryProfilesBlockedList = $newObj->getIsDeliveryProfilesBlockedList();
		$this->format = $newObj->getFormat();
		$this->extension = $newObj->getFileExtension();
		$this->containerFormat = $newObj->getContainerFormat();
		$this->seekFromTime = $newObj->getSeekFromTime();
		$this->clipTo = $newObj->getClipTo();
		$this->playbackRate = $newObj->getPlaybackRate();
		$this->storageId = $newObj->getStorageId();
		$this->entryId = $newObj->getEntryId();
		$this->tags = $newObj->getTags();
		$this->flavorAssets = $newObj->getFlavorAssets();
		$this->flavorParamIds = $newObj->getFlavorParamIds();
		$this->remoteFileSyncs = $newObj->getRemoteFileSyncs();
		$this->manifestFileSync = $newObj->getManifestFileSync();
		$this->preferredBitrate = $newObj->getPreferredBitrate();
		$this->responseFormat = $newObj->getResponseFormat();
		$this->mediaProtocol = $newObj->getMediaProtocol();
		$this->usePlayServer = $newObj->getUsePlayServer();
		$this->playerConfig = $newObj->getPlayerConfig();
		$this->uiConfId = $newObj->getUiConfId();
		$this->edgeServerIds = $newObj->getEdgeServerIds();
		$this->serveVodFromLive = $newObj->getServeVodFromLive();
		$this->serveLiveAsVodEntryId = $newObj->getServeLiveAsVodEntryId();
		$this->urlParams = $newObj->getUrlParams();
		$this->deliveryProfileId = $newObj->getDeliveryProfileId();
		$this->sessionId = $newObj->getSessionId();
		$this->aclFlavorParamsIds = $newObj->getAclFlavorParamsIds();
		$this->isAclFlavorParamsIdsBlockedList = $newObj->getIsAclFlavorParamsIdsBlockedList();
		$this->sequence = $newObj->getSequence();
		$this->hasValidSequence = $newObj->getHasValidSequence();
		$this->defaultAudioLanguage = $newObj->getDefaultAudioLanguage();
	}
}

