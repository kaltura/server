<?php
/**
 * @package plugins.cielo24
 * @subpackage api.objects
 */
class KalturaCielo24JobProviderData extends KalturaIntegrationJobProviderData
{
	/**
	 * Entry ID
	 * @var string
	 */
	public $entryId;
	
	/**
	 * Flavor ID
	 * @var string
	 */
	public $flavorAssetId;
	
	/**
	 * Caption formats
	 * @var string
	 */
	public $captionAssetFormats;
	
	/**
	 * @var KalturaCielo24Priority
	 */
	public $priority;
	
	/**
	 * @var KalturaCielo24Fidelity
	 */
	public $fidelity;
	
	/**
	 * Api key for service provider
	 * @var string
	 * @readonly
	 */
	public $username;
	
	/**
	 * Api key for service provider
	 * @var string
	 * @readonly
	 */
	public $password;

	/**
	 * Base url for service provider
	 * @var string
	 * @readonly
	 */
	public $baseUrl;
	
	/**
	 * Transcript content language
	 * @var KalturaLanguage
	 */
	public $spokenLanguage;
	
	/**
	 * should replace remote media content
	 * @var bool
	 */
	public $replaceMediaContent;
	
	/**
	 * additional parameters to send to Cielo24
	 * @var string
	 */
	public $additionalParameters;
	
	private static $map_between_objects = array
	(
		"entryId",
		"flavorAssetId",
		"captionAssetFormats",
		"priority",
		"fidelity",
		"username",
		"password",
		"baseUrl",
		"spokenLanguage",
		"replaceMediaContent",
		"additionalParameters",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		$entryId = $this->entryId;
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry || $entry->getType() != entryType::MEDIA_CLIP || !in_array($entry->getMediaType(), array(entry::ENTRY_MEDIA_TYPE_VIDEO,entry::ENTRY_MEDIA_TYPE_AUDIO)))
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_ID, $entryId);
	
		$flavorAssetId = $this->flavorAssetId;
		if($flavorAssetId)
		{
			$flavorAsset = assetPeer::retrieveById($flavorAssetId);
			if(!$flavorAsset || $flavorAsset->getEntryId() != $entryId)
				throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $flavorAssetId);
		}
		
		$cielo24ParamsMap = kConf::get('cielo24','integration');
		$supportedLanguages = $cielo24ParamsMap['languages'];
		if($this->spokenLanguage)
		{
			if (!isset($supportedLanguages[$this->spokenLanguage]))
				throw new KalturaAPIException(KalturaCielo24Errors::LANGUAGE_NOT_SUPPORTED, $this->spokenLanguage);
		}
	
		return parent::validateForUsage($sourceObject, $propertiesToSkip = array());
	}
	
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		$object = parent::toObject($object_to_fill, $props_to_skip);
	
		$entryId = $object->getEntryId();
		$entry = entryPeer::retrieveByPK($entryId);
		$partnerId = $entry->getPartnerId();
	
		$cielo24OptionsObj = Cielo24Plugin::getPartnerCielo24Options($partnerId);
		$object->setUsername($cielo24OptionsObj->username);
		$object->setPassword($cielo24OptionsObj->password);
		$object->setBaseUrl($cielo24OptionsObj->baseUrl);
		
		if(!$object->getFlavorAssetId())
		{
			$sourceAsset = assetPeer::retrieveOriginalReadyByEntryId($entryId);
			if(!$sourceAsset)
				throw new KalturaAPIException(KalturaCielo24Errors::NO_FLAVOR_ASSET_FOUND, $entryId);
			$object->setFlavorAssetId($sourceAsset->getId());
		}
		
		$cielo24ParamsMap = kConf::get('cielo24','integration');
		if(!$object->getSpokenLanguage())
		{
			$object->setSpokenLanguage($cielo24ParamsMap['default_language']);
		}
	
		$formatsString = $object->getCaptionAssetFormats();
		if($formatsString)
		{
			$formatsArray = explode(',', $formatsString);
			$excludedFormats = $cielo24ParamsMap['exclude_formats'];
			$sanitizedFormatsArray = array();
			foreach($formatsArray as $format)
			{
				$format = preg_replace("/[^A-Z_]/", "", $format);
				if(!constant("KalturaCaptionType::" . $format) || in_array($format, $excludedFormats))
					throw new KalturaAPIException(KalturaCielo24Errors::INVALID_TYPES,$formatsString);
				$sanitizedFormatsArray[] = $format;
			}
			$sanitizedFormats = implode(",", $sanitizedFormatsArray);
			$object->setCaptionAssetFormats($sanitizedFormats);
		}
		else
		{
			$defaultFormats = implode(",", $cielo24ParamsMap['default_formats']);
			$object->setCaptionAssetFormats($defaultFormats);
		}
		
		if(!$object->getPriority())
			$object->setPriority($cielo24ParamsMap['default_priority']);
		
		if(!$object->getFidelity())
			$object->setFidelity($cielo24ParamsMap['default_fidelity']);
		
		return $object;
	}
}
