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

	public function setLanguage($v)		{$this->putInCustomData(self::CUSTOM_DATA_FIELD_LANGUAGE, $v);}
	public function setDefault($v)		{$this->putInCustomData(self::CUSTOM_DATA_FIELD_DEFAULT, (bool)$v);}
	public function setLabel($v)		{$this->putInCustomData(self::CUSTOM_DATA_FIELD_LABEL, $v);}
	
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
		
		$finalPath = '/api_v3/index.php/service/caption_captionAsset/action/serve';
		$finalPath .= '/captionAssetId/' . $this->getId();
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
	
	public function setFromAssetParams($dbAssetParams)
	{
		parent::setFromAssetParams($dbAssetParams);
		
		$this->setLanguage($dbAssetParams->getLanguage());
		$this->setLabel($dbAssetParams->getLabel());
	}
}