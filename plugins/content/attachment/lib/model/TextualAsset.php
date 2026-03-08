<?php
/**
 * @package plugins.transcript
 * @subpackage model
 */ 
abstract class TextualAsset extends AttachmentAsset
{
	const CUSTOM_DATA_FIELD_HUMAN_VERIFIED = "humanVerified";
	const CUSTOM_DATA_FIELD_LANGUAGE = "language";
	
	public function getHumanVerified()  {return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_HUMAN_VERIFIED);}
	public function getLanguage()       {return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_LANGUAGE);}
	
	
	public function setHumanVerified($v) {$this->putInCustomData(self::CUSTOM_DATA_FIELD_HUMAN_VERIFIED, $v);}
	public function setLanguage($v)     {$this->putInCustomData(self::CUSTOM_DATA_FIELD_LANGUAGE, $v);}
	
	public function getName()
	{
		return $this->getFilename();
	}
}
