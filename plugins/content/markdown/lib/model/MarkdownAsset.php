<?php
/**
 * @package plugins.markdown
 * @subpackage model
 */ 
class MarkdownAsset extends TextualAsset
{
	const CUSTOM_DATA_FIELD_ACCURACY = "accuracy";
	const CUSTOM_DATA_FIELD_PROVIDER_TYPE = "providerType";

	/* (non-PHPdoc)
	 * @see Baseasset::applyDefaultValues()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(MarkdownPlugin::getAssetTypeCoreValue(MarkdownAssetType::MARKDOWN));
	}

	public function getAccuracy()       {return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_ACCURACY);}
	public function getProviderType()       {return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_PROVIDER_TYPE);}
	

	public function setAccuracy($v)     {$this->putInCustomData(self::CUSTOM_DATA_FIELD_ACCURACY, $v);}
	public function setProviderType($v)     {$this->putInCustomData(self::CUSTOM_DATA_FIELD_PROVIDER_TYPE, $v);}
	
	public function getName()
	{
		return $this->getFilename();
	}

	public function getTypeFolderName()
	{
		return 'markdown';
	}
}
