<?php
/**
 * @package plugins.transcript
 * @subpackage model
 */ 
class TranscriptAsset extends AttachmentAsset
{
	const CUSTOM_DATA_FIELD_ACCURACY = "accuracy";
	const CUSTOM_DATA_FIELD_HUMAN_VERIFIED = "humanVerified";
	const CUSTOM_DATA_FIELD_LANGUAGE = "language";
	const CUSTOM_DATA_FIELD_PROVIDER_TYPE = "providerType";

	/* (non-PHPdoc)
	 * @see Baseasset::applyDefaultValues()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(TranscriptPlugin::getAssetTypeCoreValue(TranscriptAssetType::TRANSCRIPT));
	}

	public function getAccuracy()       {return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_ACCURACY);}
	public function getHumanVerified()  {return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_HUMAN_VERIFIED);}
	public function getLanguage()       {return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_LANGUAGE);}
	public function getProviderType()       {return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_PROVIDER_TYPE);}
	

	public function setAccuracy($v)     {$this->putInCustomData(self::CUSTOM_DATA_FIELD_ACCURACY, $v);}
	public function setHumanVerified($v) {$this->putInCustomData(self::CUSTOM_DATA_FIELD_HUMAN_VERIFIED, $v);}
	public function setLanguage($v)     {$this->putInCustomData(self::CUSTOM_DATA_FIELD_LANGUAGE, $v);}
	public function setProviderType($v)     {$this->putInCustomData(self::CUSTOM_DATA_FIELD_PROVIDER_TYPE, $v);}
	
	public function getName()
	{
		return $this->getFilename();
	}

}
