<?php
/**
 * Subclass for representing a row from the 'asset' table, used for caption_assets
 *
 * @package plugins.caption
 * @subpackage model
 */ 
class CaptionAsset extends asset
{
	const CUSTOM_DATA_FIELD_LANGUAGE = "language";
	const CUSTOM_DATA_FIELD_DEFAULT = "default";
	const CUSTOM_DATA_FIELD_LABEL = "label";
	const CUSTOM_DATA_PARENT_ID = "parentId";
	const CUSTOM_DATA_ACCURACY = "accuracy";
	
	const MULTI_LANGUAGE = 'Multilingual';

	/* (non-PHPdoc)
	 * @see Baseasset::applyDefaultValues()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION));
	}

	public function getLanguage()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_LANGUAGE);}
	public function getDefault()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_DEFAULT);}
	public function getLabel()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_LABEL);}
	public function getParentId()       {return $this->getFromCustomData(self::CUSTOM_DATA_PARENT_ID);}
	public function getAccuracy()       {return $this->getFromCustomData(self::CUSTOM_DATA_ACCURACY);}

	public function setLanguage($v)		{$this->putInCustomData(self::CUSTOM_DATA_FIELD_LANGUAGE, $v);}
	public function setDefault($v)		{$this->putInCustomData(self::CUSTOM_DATA_FIELD_DEFAULT, (bool)$v);}
	public function setLabel($v)		{$this->putInCustomData(self::CUSTOM_DATA_FIELD_LABEL, $v);}
	public function setParentId($v)     {$this->putInCustomData(self::CUSTOM_DATA_PARENT_ID, $v);}
	public function setAccuracy($v)     {$this->putInCustomData(self::CUSTOM_DATA_ACCURACY, $v);}
	
	public function getFinalDownloadUrlPathWithoutKs()
	{
		$finalPath = '/api_v3/index.php/service/caption_captionAsset/action/serve';
		$finalPath .= '/captionAssetId/' . $this->getId();
		if($this->getVersion() > 1)
		{
			$finalPath .= '/v/' . $this->getVersion();
		}
		
		$partner = PartnerPeer::retrieveByPK($this->getPartnerId());
		$entry = $this->getentry();
		
		$partnerVersion = $partner->getFromCustomData('cache_caption_version');
		$entryVersion = $entry->getFromCustomData('cache_caption_version');
		
		$finalPath .= ($partnerVersion ? "/pv/$partnerVersion" : '');
		$finalPath .= ($entryVersion ? "/ev/$entryVersion" : '');
		
		return $finalPath;
	}
	
	public function setFromAssetParams($dbAssetParams)
	{
		parent::setFromAssetParams($dbAssetParams);
		
		$this->setLanguage($dbAssetParams->getLanguage());
		$this->setLabel($dbAssetParams->getLabel());
	}
	
	public function getName()
	{
		return $this->getLanguage();
	}
	
	public function shouldCopyOnReplacement() {return false;}
}
