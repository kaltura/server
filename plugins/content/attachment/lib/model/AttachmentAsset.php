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

	public function setFilename($v)		{$this->putInCustomData(self::CUSTOM_DATA_FIELD_FILENAME, $v);}
	public function setTitle($v)		{$this->putInCustomData(self::CUSTOM_DATA_FIELD_TITLE, $v);}
	
	public function getFinalDownloadUrlPathWithoutKs()
	{
		$finalPath = '/api_v3/index.php/service/attachment_attachmentAsset/action/serve';
		$finalPath .= '/attachmentAssetId/' . $this->getId();
		if($this->getVersion() > 1)
		{
			$finalPath .= '/v/' . $this->getVersion();
		}
		
		$partner = PartnerPeer::retrieveByPK($this->getPartnerId());
		$entry = $this->getentry();
		
		$partnerVersion = $partner->getFromCustomData('cache_attachment_version');
		$entryVersion = $entry->getFromCustomData('cache_attachment_version');
		
		$finalPath .= ($partnerVersion ? "/pv/$partnerVersion" : '');
		$finalPath .= ($entryVersion ? "/ev/$entryVersion" : '');
		
		return $finalPath;
	}
	
	public function getName()
	{
		return $this->getFilename();
	}
}