<?php
/**
 * Subclass for representing a row from the 'flavor_asset' table, used for thumb_assets
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class thumbAsset extends asset
{
	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setFileExt('jpg');
		$this->setType(assetType::THUMBNAIL);
	}
	
	public function getDownloadUrlWithExpiry($expiry, $useCdn = false)
	{
		$ksStr = "";
		$partnerId = $this->getPartnerId();
		$partner = PartnerPeer::retrieveByPK($partnerId);
		$secret = $partner->getSecret();
		$privilege = ks::PRIVILEGE_DOWNLOAD.":".$this->getEntryId();
		$result = kSessionUtils::startKSession($partnerId, $secret, null, $ksStr, $expiry, false, "", $privilege);
		
		if ($result < 0)
			throw new Exception("Failed to generate session for thumbnal asset [".$this->getId()."]");
		
		$finalPath = '/api_v3/index.php/service/thumbAsset/action/serve';
		$finalPath .= '/thumbAssetId/' . $this->getId();
		$finalPath .= '/ks/' . $ksStr;
			
		if($useCdn)
			$downloadUrl = myPartnerUtils::getCdnHost($partnerId) . $finalPath;
		else
			$downloadUrl = requestUtils::getRequestHost() . $finalPath;
		
		return $downloadUrl;
	}
}