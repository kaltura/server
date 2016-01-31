<?php


/**
 * @package Core
 * @subpackage model
 */
abstract class DeliveryProfile extends BaseDeliveryProfile implements IBaseObject
{
	abstract protected function buildServeFlavors();
	
	protected $DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	
	const DYNAMIC_ATTRIBUTES_FULL_SUPPORT = 0;		// the profile fully supports the required attirbutes
	const DYNAMIC_ATTRIBUTES_PARTIAL_SUPPORT = 1;	// the profile may support the required attirbutes however its better to try and find a more suitable profile
	const DYNAMIC_ATTRIBUTES_NO_SUPPORT = 2;		// the profile doesn't support the required attirbutes
	
	/**
	 * @var DeliveryProfileDynamicAttributes
	 */
	protected $params;

	public function __construct()
	{
		parent::__construct();
		$this->params = new DeliveryProfileDynamicAttributes();
	}
	
	public function serve()
	{
		$flavors = $this->buildServeFlavors();
		
		if(!$flavors)
			return null;
		
		if($this->params->getEdgeServerIds() && count($this->params->getEdgeServerIds()))
			$this->applyFlavorsDomainPrefix($flavors);
		
		$renderer = $this->getRenderer($flavors);
		
		return $renderer;
	}
	
	/**
	 * This function clones a delivery profile and create a new one out of it.
	 * @param DeliveryProfile $newObject The delivery profile we'd like to fill.
	 */
	public function cloneToNew ( $newObject )
	{
		$this->copyInto($newObject);
		$newObject->setParentId($this->getId());
		$newObject->setIsDefault(false);
		$newObject->setRecognizer($this->getRecognizer());
		$newObject->setTokenizer($this->getTokenizer());
		$newObject->save(null, true);
		return $newObject;
	}

	/**
	 * @param string $protocol
	 * @return boolean
	 */
	public function isProtocolSupported($protocol) {
		if(!$this->getMediaProtocols()){ // null means all protocols are allowed
			return true;
		}

		$mediaProtocols = explode(',', $this->getMediaProtocols());
		return in_array($protocol, $mediaProtocols);
	}

	/**
	 * returns whether the delivery profile supports the passed deliveryAttributes such as mediaProtocol, flv support, etc..
	 * @param DeliveryProfileDynamicAttributes $deliveryAttributes
	 */
	public function supportsDeliveryDynamicAttributes(DeliveryProfileDynamicAttributes $deliveryAttributes) {
		if(!$deliveryAttributes->getMediaProtocol())
			return self::DYNAMIC_ATTRIBUTES_FULL_SUPPORT;

 		if(!is_null($this->getMediaProtocols()))
		{
			$supportedProtocols = explode(",", $this->getMediaProtocols());
			if(!in_array($deliveryAttributes->getMediaProtocol(), $supportedProtocols)) 
				return self::DYNAMIC_ATTRIBUTES_NO_SUPPORT;
		}
		
		return self::DYNAMIC_ATTRIBUTES_FULL_SUPPORT;
	}
	
	/**
	 * Derives the delivery profile dynamic attributes from the file sync and the flavor asset.
	 * @param FileSync $fileSync
	 * @param flavorAsset $flavorAsset
	 */
	public function initDeliveryDynamicAttributes(FileSync $fileSync = null, flavorAsset $flavorAsset = null) {
		if ($flavorAsset)
			$this->params->setContainerFormat($flavorAsset->getContainerFormat());
	
		if($flavorAsset && $flavorAsset->getFileExt() !== null) // if the extension is missing use the one from the actual path
			$this->params->setFileExtension($flavorAsset->getFileExt());
		else if ($fileSync) 
			$this->params->setFileExtension(pathinfo($fileSync->getFilePath(), PATHINFO_EXTENSION));
	}

	/**
	 * This function returns the DeliveryProfileDynamicAttributes object
	 * @return DeliveryProfileDynamicAttributes
	 */
	public function getDynamicAttributes()
	{
		return $this->params;
	}

	/**
	 * Copies the parameters from a given DeliveryProfileDynamicAttributes object to the current object params 
	 * @param DeliveryProfileDynamicAttributes $params 
	 */
	public function setDynamicAttributes(DeliveryProfileDynamicAttributes $params) {
		$this->params->cloneAttributes($params);
	}
	
	// -------------------------------------
	// -- Override base methods ------------
	// -------------------------------------
	
	/**
	 * This function returns the recognizer this delivery profile is working with
	 * @return kUrlRecognizer
	 */
	public function getRecognizer()
	{
		$serializedObject = parent::getRecognizer();
		try {
			$object = unserialize($serializedObject);
		}
		catch (Exception $e) {
			KalturaLog::err('Error unserializing recognizer for delivery id ['.$this->getId().']');
			$object = null;
		}
		if ($object instanceof kUrlRecognizer) {
			return $object;
		}
		return null;
	}
	
	/**
	 * @param kUrlRecognizer $newObject
	 */
	public function setRecognizer($newObject)
	{
		if(is_null($newObject)) {
			parent::setRecognizer(null);
		} 
		else if ($newObject instanceof kUrlRecognizer)
		{
			$serializedObject = serialize($newObject);
			parent::setRecognizer($serializedObject);
		}
		else
		{
			KalturaLog::err('Given input is not an instance of kUrlRecognizer - ignoring');
		}
	}	
	
	/**
	 * This function returns the tokenizer this delivery profile is working with
	 * @return kUrlRecognizer
	 */
	public function getTokenizer()
	{
		$serializedObject = parent::getTokenizer();
		
		try {
			$object = unserialize($serializedObject);
		}
		catch (Exception $e) {
			KalturaLog::err('Error unserializing tokenizer for delivery id ['.$this->getId().']');
			$object = null;
		}
		
		if ($object instanceof kUrlTokenizer) {
			return $object;
		}
		return null;
	}
	
	/**
	 * @param kUrlTokenizer $newObject
	 */
	public function setTokenizer($newObject)
	{
		if(is_null($newObject)) {
			parent::setTokenizer(null);
		}
		else if ($newObject instanceof kUrlTokenizer)
		{
			$serializedObject = serialize($newObject);
			parent::setTokenizer($serializedObject);
		}
		else
		{
			KalturaLog::err('Given input is not an instance of kUrlTokenizer - ignoring');
		}
	}
	
	/**
	 * Host name is derived from the URL object.
	 * @see BaseDeliveryProfile::setUrl()
	 */
	public function setUrl($url) {
		$hostName = parse_url($url, PHP_URL_HOST);
		$this->setHostName($hostName);
		parent::setUrl($url);
	}
	
	public function setEntryId($entryId) {
		return $this->params->setEntryId($entryId);
	}
	
	public function setStorageId($storageId) {
		return $this->params->setStorageId($storageId);
	}
	
	// -------------------------------------
	// -----  DeliveryProfile functionality--------
	// -------------------------------------
	
	/**
	 * @param string baseUrl
	 * @param array $flavorUrls
	 */
	public function finalizeUrls(&$baseUrl, &$flavorsUrls)
	{
		return;
	}
	
	// -------------------------------------
	// -----   Serve functionality  --------
	// -------------------------------------
	
	protected function getRendererClass() {
		return $this->DEFAULT_RENDERER_CLASS;
	}
	
	protected function getRenderer($flavors)
	{
		$class = null;
		if ($this->params->getResponseFormat())
		{
			$formatMapping = array(
					'f4m' => 	'kF4MManifestRenderer',
					'f4mv2' => 	'kF4Mv2ManifestRenderer',
					'smil' => 	'kSmilManifestRenderer',
					'm3u8' => 	'kM3U8ManifestRenderer',
					'jsonp' => 	'kJSONPManifestRenderer',
					'json' => 	'kJSONManifestRenderer',
					'redirect' => 'kRedirectManifestRenderer',
			);
	
			if (isset($formatMapping[$this->params->getResponseFormat()]))
				$class = $formatMapping[$this->params->getResponseFormat()];
		}
	
		if (!$class)
			$class = $this->getRendererClass();
	
		$renderer = new $class($flavors, $this->params->getEntryId());
		return $renderer;
	}
	
	protected function getAudioLanguage($flavor) 
	{
		$mediaInfoObj = mediaInfoPeer::retrieveByFlavorAssetId($flavor->getId());
		if (!$mediaInfoObj) 
			return null;
		
		$contentStreams = $mediaInfoObj->getContentStreams();
		if (!isset($contentStreams)) 
			return null;
		
		$parsedJson = json_decode($contentStreams,true);
		if (!isset($parsedJson['audio'][0]['audioLanguage'])) 
			return null;
		
		$audioLanguage = $parsedJson['audio'][0]['audioLanguage'];
		if (defined('LanguageKey::' . strtoupper($audioLanguage))) {
			$audioLanguageName = constant('LanguageKey::' . strtoupper($audioLanguage));
		}
		else {
			$audioLanguageName = "Unknown ($audioLanguage)";
			KalturaLog::info("Language code [$audioLanguage] was not found. Setting [$audioLanguageName] instead");	                    
		}
	    
		return array($audioLanguage, $audioLanguageName);
	}
	
	/**
	 * @param string $url
	 * @param string $urlPrefix
	 * @param flavorAsset|flavorParams $flavor
	 * @return array
	 */
	protected function getFlavorAssetInfo($url, $urlPrefix = '', $flavor = null)
	{
		$ext = null;
		$audioLanguage = null;
		$audioLanguageName = null;
		if ($flavor) {
			if (is_callable(array($flavor, 'getFileExt'))) {
				$ext = $flavor->getFileExt();
			}
			//Extract the audio language code from flavor
			if ($flavor->hasTag(assetParams::TAG_AUDIO_ONLY)) {
				$audioLanguageData = $this->getAudioLanguage($flavor);
				if (!$audioLanguageData) {
					$audioLanguage = 'und';
					$audioLanguageName = 'Undefined';
				}
				else {
					list($audioLanguage, $audioLanguageName) = $audioLanguageData;
				}
			}
		}
		if (!$ext)
		{
			if($urlPrefix && $url)
				$urlPrefix = $urlPrefix ."/";
			$urlPath = parse_url($urlPrefix . $url, PHP_URL_PATH);
			$ext = pathinfo($urlPath, PATHINFO_EXTENSION);
		}
	
		$bitrate = ($flavor ? $flavor->getVideoBitrate() : 0);
		$width =   ($flavor ? $flavor->getWidth()   : 0);
		$height =  ($flavor ? $flavor->getHeight()  : 0);
	
		return array(
				'url' => $url,
				'urlPrefix' => $urlPrefix,
				'ext' => $ext,
				'bitrate' => $bitrate,
				'width' => $width,
				'height' => $height,
				'audioLanguage' => $audioLanguage,
				'audioLanguageName' => $audioLanguageName);
	}
	
	public function getCacheInvalidationKeys()
	{
		return array("deliveryProfile:id=".strtolower($this->getId()), "deliveryProfile:partnerId=".strtolower($this->getPartnerId()));
	}
	
	public function applyFlavorsDomainPrefix(&$flavors)
	{
		foreach ($flavors as &$flavor)
		{
			if(isset($flavor['domainPrefix']))
				continue;
			
			$domainPrefix = $this->getDeliveryServerNodeUrl();
			if($domainPrefix)
				$flavor['domainPrefix'] = $domainPrefix;
		}
	}
	
	public function getDeliveryServerNodeUrl($removeAfterUse = false)
	{
		$deliveryUrl = null;
	
		$deliveryNodeIds = $this->params->getEdgeServerIds();
		$deliveryNodes = ServerNodePeer::retrieveRegisteredServerNodesArrayByPKs($deliveryNodeIds);
	
		if(!count($deliveryNodes))
		{
			KalturaLog::debug("No active delivery nodes found among the requested edge list: " . print_r($deliveryNodeIds, true));
			return null;
		}
		
		$deliveryNode = null;
		foreach ($deliveryNodes as $node)
		{
			/* @var $node EdgeServerNode */
			if($node->validateEdgeTreeRegistered())
			{
				$deliveryNode = $node;
				break;
			}
		}
		
		if(!$deliveryNode)
		{
			KalturaLog::debug("Active edges were found but non of them is active, Failed to build valid serving route");
			return null;
		}
		
		$deliveryUrl = $deliveryNode->getPlaybackHost($this->params->getMediaProtocol(), $this->params->getFormat(), $this->getType());
	
		if(count($deliveryNodes) && $removeAfterUse)
			$this->params->setEdgeServerIds(array_diff($deliveryNodeIds, array($deliveryNode->getId())));
	
		return $deliveryUrl;
	}
	
	public function setExtraParams($v)
	{
		$this->putInCustomData("extraParams", $v);
	}
	
	public function getExtraParams()
	{
		return $this->getFromCustomData("extraParams");
	}
} 
