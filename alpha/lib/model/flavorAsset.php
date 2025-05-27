<?php

/**
 * Subclass for representing a row from the 'flavor_asset' table.
 *
 *
 *
 * @package Core
 * @subpackage model
 */
class flavorAsset extends exportableAsset
{

	const KALTURA_TOKEN_MARKER = '{kt}';
	const KALTURA_TOKEN_PARAM_NAME = '/kt/';
	const CUSTOM_DATA_FIELD_LANGUAGE = "language";
	const CUSTOM_DATA_FIELD_LABEL = "label";
	const CUSTOM_DATA_FIELD_DEFAULT = "default";
	
	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(assetType::FLAVOR);
	}

	/**
	 * Gets an array of assetParamsOutput objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this asset has previously been saved, it will retrieve
	 * related assetParamsOutputs from storage. If this asset is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array flavorParamsOutput[]
	 * @throws     PropelException
	 */
	public function getflavorParamsOutputs($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(assetPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collassetParamsOutputs === null) {
			if ($this->isNew()) {
			   $this->collassetParamsOutputs = array();
			} else {

				$criteria->add(assetParamsOutputPeer::FLAVOR_ASSET_ID, $this->id);

				assetParamsOutputPeer::addSelectColumns($criteria);
				$this->collassetParamsOutputs = assetParamsOutputPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(assetParamsOutputPeer::FLAVOR_ASSET_ID, $this->id);

				assetParamsOutputPeer::addSelectColumns($criteria);
				if (!isset($this->lastassetParamsOutputCriteria) || !$this->lastassetParamsOutputCriteria->equals($criteria)) {
					$this->collassetParamsOutputs = assetParamsOutputPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastassetParamsOutputCriteria = $criteria;
		return $this->collassetParamsOutputs;
	}

	/**
	 * Get the associated assetParams object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     assetParams The associated assetParams object.
	 * @throws     PropelException
	 */
	public function getFlavorParams(PropelPDO $con = null)
	{
		return assetParamsPeer::retrieveByPk($this->flavor_params_id);
	}
	
	public function getIsWeb()
	{
		return $this->hasTag(flavorParams::TAG_WEB);
	}

	public function setFromAssetParams($dbAssetParams)
	{
		parent::setFromAssetParams($dbAssetParams);
		
		$this->setBitrate($dbAssetParams->getVideoBitrate()+$dbAssetParams->getAudioBitrate());
		$this->setFrameRate($dbAssetParams->getFrameRate());
		$this->setVideoCodecId($dbAssetParams->getVideoCodec());
	}

	/**
	 * Code to be run after inserting to database
	 * @param PropelPDO $con 
	 */
	public function postInsert(PropelPDO $con = null)
	{
		parent::postInsert($con);
		
		$this->syncEntryFlavorParamsIds();		
	}

	/**
	 * Code to be run after updating the object in database
	 * @param PropelPDO $con
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::postUpdate($con);

		$syncFlavorParamsIds = false;
		if($this->isColumnModified(assetPeer::STATUS)){ 
			$syncFlavorParamsIds = true;
		}
		
		$ret = parent::postUpdate($con);
		
		if($syncFlavorParamsIds)
        	$this->syncEntryFlavorParamsIds();	
        	
        return $ret;
	}
	
	protected function syncEntryFlavorParamsIds()
	{
		if ($this->getStatus() == self::ASSET_STATUS_DELETED || $this->getStatus() == self::ASSET_STATUS_READY)
		{
			$entry = $this->getentry();
	    	if (!$entry)
	    	{
	        	KalturaLog::err('Cannot get entry object for flavor asset id ['.$this->getId().']');
	    	}
	    	elseif ($entry->getStatus() != entryStatus::DELETED)
	    	{
	        	$entry->syncFlavorParamsIds();
	        	$entry->save();
	    	}
		}
	}
	
	public function linkFromAsset(asset $fromAsset)
	{
		parent::linkFromAsset($fromAsset);
		$this->setBitrate($fromAsset->getBitrate());
		$this->setFrameRate($fromAsset->getFrameRate());
		$this->setVideoCodecId($fromAsset->getVideoCodecId());
		$this->setLabel($fromAsset->getLabel());
		$this->setLanguage($fromAsset->getLanguage());
		$this->setIsOriginal($fromAsset->getIsOriginal());
	}
	
	public function getInterFlowCount() { return $this->getFromCustomData("interFlowCount"); }
	public function incrementInterFlowCount() { $this->putInCustomData("interFlowCount", $this->getInterFlowCount() ? $this->getInterFlowCount()+1 : 1); }
	public function removeInterFlowCount() { $this->removeFromCustomData("interFlowCount"); }


	public function setContainsAudio($v)	{$this->putInCustomData('containsAudio', $v);}
	public function getContainsAudio()	{return $this->getFromCustomData('containsAudio');}


	public function getLanguage()
	{
		$languageCode = $this->getFromCustomData(self::CUSTOM_DATA_FIELD_LANGUAGE);
		$obj = languageCodeManager::getObjectFromTwoCode($languageCode);
		return !is_null($obj) ? $obj[languageCodeManager::KALTURA_NAME] : $languageCode;
	}
	public function setLanguage($v)
	{
		$key = languageCodeManager::getLanguageKey($v,$v);
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_LANGUAGE, $key);
	}

	public function getLabel()  {return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_LABEL); }
	public function setLabel($v){$this->putInCustomData(self::CUSTOM_DATA_FIELD_LABEL, $v);}
	
	public function getDefault()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_DEFAULT, null, false);}
	public function setDefault($v)		{$this->putInCustomData(self::CUSTOM_DATA_FIELD_DEFAULT, (bool)$v);}

	/**
	 * @param int $type
	 * @return flavorAsset
	 */
	public static function getInstance($type = null)
	{
		if(!$type || $type == assetType::FLAVOR)
			$obj = new flavorAsset();
		else
		{
			$obj = KalturaPluginManager::loadObject('flavorAsset', $type);
			if(!$obj)
				$obj = new flavorAsset();
		}
		return $obj;
	}
	
	public function getVideoBitrate()
	{
		return $this->getBitrate();
	}

	public function getCodecString()
	{
		$videoStream = null;
		$audioStream = null;
		
		$mediaInfo = $this->getMediaInfo();
		if(!$mediaInfo)
		{
			return '';
		}
		
		$contentStreams = json_decode($mediaInfo->getContentStreams(), true);
		if(!$contentStreams)
		{
			return '';
		}
		
		foreach ($contentStreams as $key => $value)
		{
			if($key == "video")
			{
				$videoStream = $value[0];
			}
			elseif($key == "audio")
			{
				$audioStream = $value[0];
			}
		}
		
		if(!$videoStream['extradata'])
		{
			return '';
		}
		
		// Handle video codec
		$videoCodecString = '';
		if ($videoStream)
		{
			$vCodec = strtolower($videoStream['videoFormat']);
			
			switch ($vCodec)
			{
				case 'h264':
					$videoCodecString = $this->getAvc1Codec($videoStream['extradata'] ?? '');
					break;
				
				case 'hevc':
				case 'h265':
					$videoCodecString = $this->getHvc1Codec($videoStream['extradata'] ?? '');
					break;
				
				case 'av1':
					$videoCodecString = $this->getAv1Codec($videoStream);
					break;
			}
		}
		
		// Handle audio codec
		$audioCodecString = '';
		if ($audioStream)
		{
			$aCodec = strtolower($audioStream['audioFormat']);
			$aProfile = $audioStream['containerProfile'] ?? '';
			$audioCodecString = $this->getAudioCodec($aCodec, $aProfile, $videoStream['extradata'] ?? '');
		}
		
		return implode(',', array_filter(array($videoCodecString, $audioCodecString)));
	}
	
	/**
	 * Get AVC1 codec string
	 * @param string $extraDataRaw
	 * @return string|null
	 */
	private function getAvc1Codec(string $extraDataRaw)
	{
		$bytes = $this->hexStringToBytes($extraDataRaw);
		
		$pos = strpos($bytes, "\x67");
		if ($pos === false)
			return null;  // no SPS found
		
		// Check length to avoid reading past buffer
		$profile_idc = isset($bytes[$pos + 1]) ? ord($bytes[$pos + 1]) : 0x42;
		$constraint_flags = isset($bytes[$pos + 2]) ? ord($bytes[$pos + 2]) : 0x00;
		$level_idc = isset($bytes[$pos + 3]) ? ord($bytes[$pos + 3]) : 0x1e;
		
		// Format as two-digit hex lowercase
		$profileHex = sprintf('%02x', $profile_idc);
		$constraintsHex = sprintf('%02x', $constraint_flags);
		$levelHex = sprintf('%02x', $level_idc);
		
		return "avc1." . $profileHex . $constraintsHex . $levelHex;
	}
	
	/**
	 * Get HVC1 codec string
	 * @param string $extraDataRaw
	 * @return string|null
	 */
	private function getHvc1Codec(string $extraDataRaw)
	{
		$bytes = $this->hexStringToBytes($extraDataRaw);
		
		// Check if conversion was successful
		if ($bytes === false || strlen($bytes) < 19)
		{
			return null; // Invalid hex string or too short
		}
		
		// ✅ Extract the first 19 bytes
		$profile_tier_level = ord($bytes[1]);
		
		// ✅ Extract the profile IDC (first 5 bits of byte[1])
		$profile_idc = $profile_tier_level & 0x1F;
		
		// ✅ Extract the correct nibble (first nibble of byte[2])
		$profile_compat_byte = (ord($bytes[2]) >> 4); // e.g. 0x60 >> 4 = 6
		
		// ✅ Extract the tier flag (bit 5 of byte[1])
		$tier_flag = ($profile_tier_level & 0x20) ? 'H' : 'L';
		
		// ✅ Extract the level IDC (byte[12])
		$level_idc = ord($bytes[12]);
		
		// Constraint Indicator Flags: bytes 6–11
		$constraint_bytes = unpack('C6', substr($bytes, 6, 6));
		$constraint_flags = '';
		
		// Iterate through the constraint bytes and convert to hex
		foreach ($constraint_bytes as $byte)
		{
			if ($byte !== 0 || $constraint_flags !== '')
			{
				$constraint_flags .= sprintf('%02X', $byte);
			}
		}
		
		// Limit to 4 characters
		$constraint_flags = strtolower(substr($constraint_flags, 0, 2));
		
		return sprintf("hvc1.%d.%d.%s%d.%s", $profile_idc, $profile_compat_byte, $tier_flag, $level_idc, $constraint_flags);
	}
	
	/**
	 * Get AV1 codec string
	 * @param array $stream
	 * @return string
	 */
	private function getAv1Codec($stream)
	{
		$profileMap = [
			'Main' => 0,
			'High' => 1,
			'Professional' => 2
		];
		
		$profile = $profileMap[$stream['profile']] ?? 0;
		$level = $stream['videoLevel'] ?? 8; // Level 4.0
		$tier = 'M'; // assume Main tier; you could add ffprobe tier_flag later
		$bitDepth = $stream['bit_depth'] ?? 8;
		
		// Ensure two-digit level and bit depth
		$levelStr = str_pad($level, 2, '0', STR_PAD_LEFT);
		$bitDepthStr = str_pad($bitDepth, 2, '0', STR_PAD_LEFT);
		
		return "av01.{$profile}.{$levelStr}{$tier}.{$bitDepthStr}";
	}
	
	/**
	 * Convert a hex string to binary data
	 * @param string $extraDataRaw
	 * @return string|false
	 */
	private function hexStringToBytes($extraDataRaw)
	{
		// Assuming the extraDataRaw is in hex format, we need to parse it
		$lines = explode("\n", $extraDataRaw);
		$hexString = '';
		foreach ($lines as $line) {
			// Match the hex values after the offset
			if (preg_match('/^[0-9a-f]+:\s+([0-9a-f\s]+)/i', $line, $matches)) {
				$hexString .= str_replace(' ', '', $matches[1]);  // Remove spaces and concatenate
			}
		}
		
		// Convert hex string to binary
		$hex = preg_replace('/\s+/', '', $hexString);
		
		// Convert hex to binary
		return hex2bin($hex);
	}
	
	private function getAudioCodec(string $codecName, string $profile = '', ?string $extraData = null)
	{
		switch (strtolower($codecName))
		{
			case 'aac':
				switch (strtolower($profile))
				{
					case 'lc':
					case 'low complexity':
						return 'mp4a.40.2';
					case 'he-aac':
					case 'high efficiency aac':
						return 'mp4a.40.5';
					case 'he-aacv2':
						return 'mp4a.40.29';
					case 'main':
						return 'mp4a.40.1';
					default:
						// Optional: fallback to extradata parsing
						if ($extraData)
						{
							return $this->parseAacFromExtraData($extraData) ?? 'mp4a.40.2';
						}
						return 'mp4a.40.2';
				}
			
			case 'mp3':
				return 'mp4a.69'; // MPEG-1 Layer III
			
			case 'ac3':
				return 'ac-3';
			
			case 'eac3':
				return 'ec-3';
			
			case 'opus':
				return 'opus';
			
			case 'vorbis':
				return 'vorbis';
			
			case 'flac':
				return 'flac';
			
			case 'alac':
				return 'alac';
			
			default:
				return ''; // Unknown or unsupported codec
		}
	}
	
	private function parseAacFromExtraData(string $hexData): ?string
	{
		$bytes = $this->hexStringToBytes($hexData);
		if (!$bytes || strlen($bytes) < 2)
			return null;
		
		// AudioSpecificConfig (ISO/IEC 14496-3)
		// First 5 bits = audioObjectType
		$byte1 = ord($bytes[0]);
		$byte2 = ord($bytes[1]);
		
		$audioObjectType = ($byte1 >> 3) & 0x1F;
		
		// Optional: handle extended AOTs, not shown here
		switch ($audioObjectType)
		{
			case 1: return 'mp4a.40.1';  // Main
			case 2: return 'mp4a.40.2';  // LC
			case 5: return 'mp4a.40.5';  // SBR (HE-AAC)
			case 29: return 'mp4a.40.29';// HE-AACv2
			default: return 'mp4a.40.2'; // fallback to LC
		}
	}
	
	public function getServeFlavorUrl($previewLength = null, $fileName = null, $urlManager = null, $isDir = false)
	{
		$entry = $this->getentry();

		if (!$entry || !in_array($entry->getType(), array(entryType::MEDIA_CLIP, entryType::DOCUMENT)))
		{
			$id = $this->getId();
			throw new kCoreException("asset $id belongs to an entry of a wrong type", kCoreException::INVALID_ENTRY_TYPE);
		}

		if (!$fileName)
		{
			list($fileName , $extension) = kAssetUtils::getFileName($entry , $this);
			$fileName = str_replace("\n", ' ', $fileName);
			$fileName = kString::keepOnlyValidUrlChars($fileName);
	
			if ($extension && $extension !== kUploadTokenMgr::NO_EXTENSION_IDENTIFIER)
			{
				$fileName .= ".$extension";
			}
			else if($this->getContainerFormat())
			{
				$extension = kAssetUtils::getFileExtension($this->getContainerFormat());
				if ($extension)
				{
					$fileName .= ".$extension";
				}
			}
		}
		
		//adding a serveFlavor download parameter
		$urlParameters = "/fileName/$fileName";
		$explicitFileExt = null;
		if($isDir)
		{
			$urlParameters .= "/dirFileName/$fileName";
			$explicitFileExt = pathinfo($fileName, PATHINFO_EXTENSION);
		}

		if ($previewLength)
			$urlParameters .= "/clipTo/$previewLength";

		$url = kAssetUtils::getAssetUrl($this, false, null, null , $urlParameters, null, $urlManager, $explicitFileExt);
		
		return $url;
	}
	
	
	public function getPlayManifestUrl($clientTag, $storageProfileId = null, $mediaProtocol = PlaybackProtocol::HTTP, $addKtToken = false) {
		$entryId = $this->getEntryId();
		$partnerId = $this->getPartnerId();
		$subpId = $this->getentry()->getSubpId();
		$partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $subpId);
		$flavorAssetId = $this->getId();
		
		$url = "$partnerPath/playManifest/entryId/$entryId/flavorId/$flavorAssetId/protocol/$mediaProtocol/format/url";
		if($storageProfileId)
			$url .= "/storageId/" . $storageProfileId;

		if($addKtToken)
			$url .= self::KALTURA_TOKEN_PARAM_NAME . self::KALTURA_TOKEN_MARKER;

		if ($this->getFileExt())
			$url .= "/a." . $this->getFileExt();

		$url .= "?clientTag=$clientTag";

		if($addKtToken)
			$url = self::calculateKalturaToken($url);

		return $url;
	}
	
	public function estimateFileSize(entry $entry, $seconds) {
		$orginalSizeKB = $this->getSize();
		$size = $orginalSizeKB * ($seconds / ($entry->getLengthInMsecs() / 1000)) * 1.2;
		$size = min($orginalSizeKB, floor($size)) * 1024;
		return $size;
	}
	
	static protected function calculateKalturaToken($url)
	{
		$token = sha1(kConf::get('url_token_secret') . $url);
		return str_replace(self::KALTURA_TOKEN_MARKER, $token, $url);
	}

	protected function getSyncKeysForExporting()
	{
		return array(
			$this->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET),
			$this->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISM),
			$this->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISMC));
	}
	
	public function getKeepOldAssetOnEntryReplacement()
	{
		if($this->getentry()->getReplacementOptions()->getKeepOldAssets()) 
		{
			return true;
		}
	
		return false;
	}
	
	public function getName()
	{
		$flavorParams = $this->getFlavorParams();
		if ($flavorParams)
			return $flavorParams->getName();
		return "";
	}
	
	/* (non-PHPdoc)
 	 * @see Baseasset::copyInto()
 	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		/* @var $copyObj flavorasset */
		parent::copyInto($copyObj, $deepCopy);
		$copyObj->setLanguage($this->getLanguage());
		$copyObj->setLabel($this->getLabel());
	}

	public function shouldEncrypt()
	{
		$entry = $this->getentry();
		return ($entry && ($entry->getType() == entryType::DOCUMENT));
	}

	protected function setLanguageFromFlavorParams()
	{
		$flavorParams = $this->getFlavorParams();
		if (!$flavorParams)
			return null;
		$multiStream = $flavorParams->getMultiStream();
		if (isset($multiStream))
		{
			$multiStreamObj = json_decode($multiStream);
			if (isset($multiStreamObj->audio->languages) && count($multiStreamObj->audio->languages) > 0)
			{
				$flavorLang = $multiStreamObj->audio->languages[0];
				$this->setLanguage($flavorLang);
				return $flavorLang;
			}
		}
		return null;
	}

	public function preSave(PropelPDO $con = null)
	{
		if ($this->isColumnModified(assetPeer::FLAVOR_PARAMS_ID))
		{
			$flavorLang = $this->setLanguageFromFlavorParams();
			if ($flavorLang)
			{
				$entry = $this->getentry();
				if (!$entry)
					throw new kCoreException("Invalid entry id [" . $this->getEntryId() . "]", APIErrors::INVALID_ENTRY_ID);
				$conversionProfile = $entry->getconversionProfile2();
				if ($conversionProfile && $conversionProfile->getDefaultAudioLang() == $flavorLang)
				{
					$this->setDefault(true);
				}

			}

		}
		return parent::preSave($con);
	}

	/**
	 * Get frameSize - width * height - CD (color depth) is not supported hench ingring in calculation
	 */
	public function getFrameSize()
	{
		return $this->height * $this->width;
	}

	/**
	 * (non-PHPdoc)
	 * @see lib/model/ISyncableFile#getTypeFolderName()
	 */
	public function getTypeFolderName()
	{
		return 'flavors';
	}

	/**
	 * @return int|null
	 * value is in milliseconds
	 */
	public function getSegmentDuration()
	{
		$tags = explode(',', $this->getTags());
		foreach ($tags as $tag)
		{
			if (strpos($tag, 'segment_duration:') !== false)
			{
				return (int)explode(':', $tag)[1];
			}
		}
		return null;
	}

}
