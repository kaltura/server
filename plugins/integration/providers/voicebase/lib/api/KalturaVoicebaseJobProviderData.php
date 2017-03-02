<?php
/**
 * @package plugins.voicebase
 * @subpackage api.objects
 */
class KalturaVoicebaseJobProviderData extends KalturaIntegrationJobProviderData
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
	 * input Transcript-asset ID
	 * @var string
	 */
	public $transcriptId;
	
	/**
	 * Caption formats
	 * @var string
	 */
	public $captionAssetFormats;
	
	/**
	 * Api key for service provider
	 * @var string
	 * @readonly
	 */
	public $apiKey;
	
	/**
	 * Api key for service provider
	 * @var string
	 * @readonly
	 */
	public $apiPassword;
	
	/**
	 * Transcript content language
	 * @var KalturaLanguage
	 */
	public $spokenLanguage;
	
	/**
	 * Transcript Content location
	 * @var string
	 * @readonly
	 */
	public $fileLocation;
	
	/**
	 * should replace remote media content
	 * @var bool
	 */
	public $replaceMediaContent;
	
	private static $map_between_objects = array
	(
		"entryId",
		"flavorAssetId",
		"transcriptId" => "inputTranscriptId",
		"captionAssetFormats",
		"apiKey",
		"apiPassword",
		"spokenLanguage",
		"fileLocation",
		"replaceMediaContent",
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
		if(!$entry)
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_ID, $entryId);
	
		$transcriptId = $this->transcriptId;
		if($transcriptId)
		{
			$transcript = assetPeer::retrieveById($transcriptId);
			if (!$transcript || $transcript->getEntryId() != $entryId || $transcript->getType() != TranscriptPlugin::getAssetTypeCoreValue(TranscriptAssetType::TRANSCRIPT))
				throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $transcriptId);
		}
	
		$voicebaseParamsMap = kConf::get('voicebase','integration');
		if(!$this->captionAssetFormats)
		{
			$defaultFormatsArray = $voicebaseParamsMap['default_formats'];
			$this->captionAssetFormats = implode(',', $defaultFormatsArray);
		}
		else
		{
			$formatsString = $this->captionAssetFormats;
			$formatsArray = explode(',', $formatsString);
	
			$excludedFormats = $voicebaseParamsMap['exclude_formats'];
	
			foreach($formatsArray as $format)
			{
				if(!constant("KalturaCaptionType::" . $format) || in_array($format, $excludedFormats))
					throw new KalturaAPIException(KalturaVoicebaseErrors::INVALID_TYPES,$formatsString);
			}
		}
	
		$supportedLanguages = $voicebaseParamsMap['languages'];
	
		if($this->spokenLanguage)
		{
			if (!isset($supportedLanguages[$this->spokenLanguage]))
				throw new KalturaAPIException(KalturaVoicebaseErrors::LANGUAGE_NOT_SUPPORTED, $this->spokenLanguage);
		}
		else
			$this->spokenLanguage = $voicebaseParamsMap['default_language'];
	
		return parent::validateForUsage($sourceObject, $propertiesToSkip = array());
	}
	
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		$object = parent::toObject($object_to_fill, $props_to_skip);
	
		$entryId = $object->getEntryId();
		$entry = entryPeer::retrieveByPK($entryId);
		$partnerId = $entry->getPartnerId();
		$transcriptId = $object->getInputTranscriptId();
	
		$voicebaseOptionsObj = VoicebasePlugin::getPartnerVoicebaseOptions($partnerId);
		$object->setApiKey($voicebaseOptionsObj->apiKey);
		$object->setApiPassword($voicebaseOptionsObj->apiPassword);
		
		if($transcriptId)
		{
			$transcript = assetPeer::retrieveById($transcriptId);
			$key = $transcript->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
			$fileSync = FileSyncPeer::retrieveByFileSyncKey($key, true);
			$object->setFileLocation($fileSync->getFullPath());
		}
		
		return $object;
	}
}
