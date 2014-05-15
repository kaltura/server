<?php


/**
 * @package Core
 * @subpackage model
 */
abstract class DeliveryProfile extends BaseDeliveryProfile {
	
	protected $DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	
	/**
	 * @var DeliveryProfileDynamicAttributes
	 */
	protected $params;

	public function __construct()
	{
		parent::__construct();
		$this->params = new DeliveryProfileDynamicAttributes();
	}
	
	/**
	 * This function clones a delivery profile and create a new one out of it.
	 * @param DeliveryProfile $newObject The delivery profile we'd like to fill.
	 */
	public function cloneToNew ( $newObject )
	{
		// TODO @_!! Asd T about fillObjectFromObject Usage
		$newObject->setCopiedFrom($this);
	
		$all_fields = DeliveryProfilePeer::getFieldNames ();
		$ignore_list = array ( "Id" , "ParentId", "IsDefault");
		
		// clone from current
		baseObjectUtils::fillObjectFromObject( $all_fields ,
				$this ,
				$newObject ,
				baseObjectUtils::CLONE_POLICY_PREFER_NEW , $ignore_list , BasePeer::TYPE_PHPNAME );
	
		$newObject->save(null, true);
		return $newObject;
	}
	
	/**
	 * Derives the delivery profile dynamic attribtues from the file sync and the flavor asset.
	 * @param FileSync $fileSync
	 * @param flavorAsset $flavorAsset
	 */
	public function initDeliveryDynamicAttribtues(FileSync $fileSync = null, flavorAsset $flavorAsset = null) {
		if ($flavorAsset)
			$this->params->setContainerFormat($flavorAsset->getContainerFormat());
	
		if($flavorAsset && $flavorAsset->getFileExt() !== null) // if the extension is missing use the one from the actual path
			$this->params->setFileExtention($flavorAsset->getFileExt());
		else if ($fileSync) 
			$this->params->setFileExtention(pathinfo($fileSync->getFilePath(), PATHINFO_EXTENSION));
	}
	
	/**
	 * Copies the parameters from a given DeliveryProfileDynamicAttributes object to the current object params 
	 * @param DeliveryProfileDynamicAttributes $params 
	 */
	public function setDynamicAttribtues(DeliveryProfileDynamicAttributes $params) {
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
		if ($newObject instanceof kUrlRecognizer)
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
		if ($newObject instanceof kUrlTokenizer)
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
		if(is_null($hostName)) {
			$path = explode("/", $url);
			$hostName = $path[0];
		}
		$this->setHostName($hostName);
		parent::setUrl($url);
	}
	
	public function setEntryId($entryId) {
		return $this->params->setEntryId($entryId);
	}
	
	public function setStorageProfileId($storageProfileId) {
		return $this->params->setStorageProfileId($storageProfileId);
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
	
	/**
	 * @param array $flavors
	 * @return string
	 */
	protected function getMimeType($flavors)
	{
		$entry = entryPeer::retrieveByPK($this->params->getEntryId());
		if ($entry->getType() == entryType::MEDIA_CLIP && count($flavors))
		{
			$isMp3 = true;
			foreach($flavors as $flavor)
			{
				if (!isset($flavor['ext']) || strtolower($flavor['ext']) != 'mp3')
					$isMp3 = false;
			}
				
			if ($isMp3)
				return 'audio/mpeg';
		}
	
		return 'video/x-flv';
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
					'redirect' => 'kRedirectManifestRenderer',
			);
	
			if (isset($formatMapping[$this->params->getResponseFormat()]))
				$class = $formatMapping[$this->params->getResponseFormat()];
		}
	
		if (!$class)
			$class = $this->getRendererClass();
	
		$renderer = new $class;
		if ($renderer instanceof kMultiFlavorManifestRenderer)
			$renderer->flavors = $flavors;
		else
			$renderer->flavor = reset($flavors);
	
		return $renderer;
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
		if ($flavor && is_callable(array($flavor, 'getFileExt')))
		{
			$ext = $flavor->getFileExt();
		}
		if (!$ext)
		{
			$urlPath = parse_url($urlPrefix . $url, PHP_URL_PATH);
			$ext = pathinfo($urlPath, PATHINFO_EXTENSION);
		}
	
		$bitrate = ($flavor ? $flavor->getBitrate() : 0);
		$width =   ($flavor ? $flavor->getWidth()   : 0);
		$height =  ($flavor ? $flavor->getHeight()  : 0);
	
		return array(
				'url' => $url,
				'urlPrefix' => $urlPrefix,
				'ext' => $ext,
				'bitrate' => $bitrate,
				'width' => $width,
				'height' => $height);
	}
	
} 
