<?php
/**
 * @package plugins.transcript
 * @subpackage model
 */ 
class TranscriptAsset extends AttachmentAsset
{
    const CUSTOM_DATA_FIELD_ACCURACY = "accuracy";
    const CUSTOM_DATA_FIELD_HUMAN_VERIFIED = "humanVerified";

	/* (non-PHPdoc)
	 * @see Baseasset::applyDefaultValues()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(AttachmentPlugin::getAssetTypeCoreValue(TranscriptAttachmentAssetType::TRANSCRIPT_ATTACHMENT));
	}

    public function getAccuracy()       {return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_ACCURACY);}
    public function getHumanVerified()  {return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_HUMAN_VERIFIED);}

    public function setAccuracy($v)     {$this->putInCustomData(self::CUSTOM_DATA_FIELD_ACCURACY, $v);}
    public function setHumanVerified($v) {$this->putInCustomData(self::CUSTOM_DATA_FIELD_HUMAN_VERIFIED, $v);}

	}
}
