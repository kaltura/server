<?php

/**
 * Subclass for representing a row from the 'asset_params' table, used for caption_params
 *
 * @package plugins.caption
 * @subpackage model
 */ 
class CaptionParams extends assetParams
{
	const CUSTOM_DATA_FIELD_LANGUAGE = "language";
	const CUSTOM_DATA_FIELD_DEFAULT = "default";
	const CUSTOM_DATA_FIELD_LABEL = "label";
	const CUSTOM_DATA_FIELD_SOURCE_PARAMS_ID = "sourceParamsId";
	
	/* (non-PHPdoc)
	 * @see BaseassetParams::applyDefaultValues()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION));
	}

	public function getLanguage()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_LANGUAGE);}
	public function getDefault()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_DEFAULT);}
	public function getLabel()				{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_LABEL);}
	public function getSourceParamsId()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_SOURCE_PARAMS_ID);}

	public function setLanguage($v)			{$this->putInCustomData(self::CUSTOM_DATA_FIELD_LANGUAGE, $v);}
	public function setDefault($v)			{$this->putInCustomData(self::CUSTOM_DATA_FIELD_DEFAULT, (bool)$v);}
	public function setLabel($v)			{$this->putInCustomData(self::CUSTOM_DATA_FIELD_LABEL, $v);}
	public function setSourceParamsId($v)	{$this->putInCustomData(self::CUSTOM_DATA_FIELD_SOURCE_PARAMS_ID, $v);}
}