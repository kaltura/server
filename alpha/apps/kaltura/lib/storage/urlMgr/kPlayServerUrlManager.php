<?php
/**
 * @package Core
 * @subpackage storage
 */
class kPlayServerUrlManager extends kUrlManager
{
	/**
	 * @var kUrlManager
	 */
	protected $urlManager;
	
	/**
	 * Base64 JSON attributes to be passed to the play-server
	 * Used for ad-server additional arguments
	 * @var string
	 */
	protected $playerConfig;
	
	public function __construct($entryId, kUrlManager $urlManager, $playerConfig)
	{	
		parent::__construct(null, null, $entryId);
		
		$this->urlManager = $urlManager;
		$this->playerConfig = $playerConfig;
	}
	
	protected function getPlayServerUrl($manifestUrl)
	{
		$entry = entryPeer::retrieveByPK($this->getEntryId());
		if(!$entry)
		{
			KalturaLog::err("Entry [$this->entryId] not found");
			return $manifestUrl;
		}
		
		$partnerId = $entry->getPartnerId();
		$playServerHost = myPartnerUtils::getPlayServerHost($partnerId, $this->protocol);
		
		$url = "$playServerHost/manifest/master";
		if(count($this->playerConfig))
			$url .= "/playerConfig/$this->playerConfig";
			
		return "$url?url=$manifestUrl";
	}
	
	/* (non-PHPdoc)
	 * @see kUrlManager::doGetFileSyncUrl()
	 */
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		return $this->getPlayServerUrl($this->urlManager->doGetFileSyncUrl($fileSync));
	}
	
	/* (non-PHPdoc)
	 * @see kUrlManager::doGetFlavorAssetUrl()
	 */
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		return $this->getPlayServerUrl($this->urlManager->doGetFlavorAssetUrl($flavorAsset));
	}
	
	/* (non-PHPdoc)
	 * @see kUrlManager::finalizeUrls()
	 */
	public function finalizeUrls(&$baseUrl, &$flavorsUrls)
	{
		return $this->urlManager->finalizeUrls($baseUrl, $flavorsUrls);
	}
	
	/* (non-PHPdoc)
	 * @see kUrlManager::getRendererClass()
	 */
	public function getRendererClass()
	{
		return $this->urlManager->getRendererClass();
	}
}