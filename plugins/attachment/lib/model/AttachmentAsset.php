<?php
/**
 * Subclass for representing a row from the 'asset' table, used for attachment_assets
 *
 * @package plugins.attachment
 * @subpackage model
 */ 
class AttachmentAsset extends asset
{
	const CUSTOM_DATA_FIELD_FILENAME = "filename";
	const CUSTOM_DATA_FIELD_TITLE = "title";
	const CUSTOM_DATA_FIELD_DESCRIPTION = "description";

	/* (non-PHPdoc)
	 * @see Baseasset::applyDefaultValues()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(AttachmentPlugin::getAssetTypeCoreValue(AttachmentAssetType::ATTACHMENT));
	}

	public function getFilename()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_FILENAME);}
	public function getTitle()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_TITLE);}
	public function getDescription()	{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_DESCRIPTION);}

	public function setFilename($v)		{$this->putInCustomData(self::CUSTOM_DATA_FIELD_FILENAME, $v);}
	public function setTitle($v)		{$this->putInCustomData(self::CUSTOM_DATA_FIELD_TITLE, $v);}
	public function setDescription($v)	{$this->putInCustomData(self::CUSTOM_DATA_FIELD_DESCRIPTION, $v);}
}