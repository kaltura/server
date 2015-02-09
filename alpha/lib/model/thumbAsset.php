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
	
	public function getFinalDownloadUrlPathWithoutKs()
	{
		$finalPath = '/api_v3/index.php/service/thumbAsset/action/serve';
		$finalPath .= '/thumbAssetId/' . $this->getId();
		if($this->getVersion() > 1)
		{
			$finalPath .= '/v/' . $this->getVersion();
		}
		
		$partner = PartnerPeer::retrieveByPK($this->getPartnerId());
		$entry = $this->getentry();
		
		$partnerVersion = $partner->getCacheThumbnailVersion();
		$entryVersion = $entry->getCacheThumbnailVersion();
		
		$finalPath .= ($partnerVersion ? "/pv/$partnerVersion" : '');
		$finalPath .= ($entryVersion ? "/ev/$entryVersion" : '');
						
		return $finalPath;
	}
}