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
	
	protected function getFinalDownloadUrlPathWithoutKs()
	{
		$finalPath = '/api_v3/index.php/service/thumbAsset/action/serve';
		$finalPath .= '/thumbAssetId/' . $this->getId();
						
		return $finalPath;
	}

	public static function removeThumbAssetDeafultTags($entryID = null, thumbAsset $thumbAsset = null)
	{
		$entryThumbAssets = array();
		if($thumbAsset)
			$entryThumbAssets = assetPeer::retrieveThumbnailsByEntryId($thumbAsset->getEntryId());
		else if($entryID)
			$entryThumbAssets = assetPeer::retrieveThumbnailsByEntryId($entryID);
			
		foreach($entryThumbAssets as $entryThumbAsset)
		{
			if($thumbAsset && $entryThumbAsset->getId() == $thumbAsset->getId())
				continue;

			if(!$entryThumbAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
				continue;

			$entryThumbAsset->removeTags(array(thumbParams::TAG_DEFAULT_THUMB));
			$entryThumbAsset->save();
		}
	}
}