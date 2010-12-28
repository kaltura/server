<?php

/**
 * Subclass for performing query and update operations on the 'media_info' table.
 *
 * 
 *
 * @package lib.model
 */ 
class mediaInfoPeer extends BasemediaInfoPeer
{
	/**
	 * @param string $flavorAssetId
	 * @return mediaInfo
	 */
	public static function retrieveByFlavorAssetId($flavorAssetId)
	{
		$criteria = new Criteria();
		$criteria->add(mediaInfoPeer::FLAVOR_ASSET_ID, $flavorAssetId);
		$criteria->addDescendingOrderByColumn(mediaInfoPeer::ID);

		return mediaInfoPeer::doSelectOne($criteria);
	}
	
	/**
	 * @param string $entryId
	 * @return mediaInfo
	 */
	public static function retrieveOriginalByEntryId($entryId)
	{
		$sourceFlavorAsset = flavorAssetPeer::retrieveOriginalByEntryId($entryId);
		if(!$sourceFlavorAsset)
			return null;
					
		$criteria = new Criteria();
		$criteria->add(mediaInfoPeer::FLAVOR_ASSET_ID, $sourceFlavorAsset->getId());

		return mediaInfoPeer::doSelectOne($criteria);
	}
}
