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
	
	public function getDownloadUrlWithExpiry($expiry, $useCdn = false)
	{
		$ksStr = "";
		$partnerId = $this->getPartnerId();
		$partner = PartnerPeer::retrieveByPK($partnerId);
		$secret = $partner->getSecret();
		$privilege = ks::PRIVILEGE_DOWNLOAD.":".$this->getEntryId();
		$result = kSessionUtils::startKSession($partnerId, $secret, null, $ksStr, $expiry, false, "", $privilege);

		if ($result < 0)
			throw new Exception("Failed to generate session for flavor asset [".$this->getId()."]");
		
		$finalPath = '/api_v3/index.php/service/attachment_attachmentAsset/action/serve';
		$finalPath .= '/attachmentAssetId/' . $this->getId();
		$finalPath .= '/ks/' . $ksStr;
			
		// Gonen May 12 2010 - removing CDN URLs. see ticket 5135 in internal mantis
		// in order to avoid conflicts with access_control (geo-location restriction), we always return the requestHost (www_host from kConf)
		// and not the CDN host relevant for the partner.
		
		// Tan-Tan January 27 2011 - in some places we do need the cdn, I added a paramter useCdn to force it.
		if($useCdn)
			$downloadUrl = myPartnerUtils::getCdnHost($partnerId) . $finalPath;
		else
			$downloadUrl = requestUtils::getRequestHost() . $finalPath;
		
		return $downloadUrl;
	}
}