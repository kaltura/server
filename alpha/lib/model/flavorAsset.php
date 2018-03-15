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
	}
	
	public function getInterFlowCount() { return $this->getFromCustomData("interFlowCount"); }
	public function incrementInterFlowCount() { $this->putInCustomData("interFlowCount", $this->getInterFlowCount() ? $this->getInterFlowCount()+1 : 1); }
	public function removeInterFlowCount() { $this->removeFromCustomData("interFlowCount"); }

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
	
	public function getServeFlavorUrl($previewLength = null, $fileName = null)
	{
		$entry = $this->getentry();

		if (!$entry || $entry->getType() != entryType::MEDIA_CLIP)
		{
			$id = $this->getId();
			throw new kCoreException("asset $id belongs to an entry of a wrong type", kCoreException::INVALID_ENTRY_TYPE);
		}

		if (!$fileName)
		{
			list($fileName , $extension) = kAssetUtils::getFileName($entry , $this);
			$fileName = str_replace("\n", ' ', $fileName);
			$fileName = kString::keepOnlyValidUrlChars($fileName);
	
			if ($extension)
				$fileName .= ".$extension";
		}
		
		//adding a serveFlavor download parameter
		$urlParameters = "/fileName/$fileName";

		if ($previewLength)
			$urlParameters .= "/clipTo/$previewLength";

		$url = kAssetUtils::getAssetUrl($this, false, null, null , $urlParameters);
		
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
		return ($entry->getType() == entryType::DOCUMENT);
	}

	public function setLanguageAndDefault()
	{
		$entry = $this->getentry();
		if (!$entry)
			throw new kCoreException("Invalid entry id [".$this->getEntryId()."]", APIErrors::INVALID_ENTRY_ID);
		$flavorParams = $this->getFlavorParams();
		$multiStream = $flavorParams->getMultiStream();
		if (isset($multiStream))
		{
			$multiStreamObj = json_decode($multiStream);
			if (isset($multiStreamObj))
			{
				if (isset($multiStreamObj->audio) && isset($multiStreamObj->audio->languages) && count($multiStreamObj->audio->languages) > 0)
				{
					$flavorLang = $multiStreamObj->audio->languages[0];
					$this->setLanguage($flavorLang);
					$conversionProfile = $entry->getconversionProfile2();
					if ($conversionProfile->getDefaultAudioLang() == $flavorLang)
					{
						$this->setDefault(true);
					}
				}
			}
		}
	}

	public function preSave(PropelPDO $con = null)
	{
		if ($this->isColumnModified(assetPeer::FLAVOR_PARAMS_ID))
		{
			$this->setLanguageAndDefault();
		}
		return parent::preSave($con);
	}


}
