<?php

/**
 * Subclass for performing query and update operations on the 'media_info' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class mediaInfoPeer extends BasemediaInfoPeer
{
	/**
	 * @param string $flavorAssetId
	 * @param int $isAscending
	 * @return mediaInfo
	 */
	public static function retrieveByFlavorAssetId($flavorAssetId, $isAscending=0)
	{
		$criteria = new Criteria();
		$criteria->add(mediaInfoPeer::FLAVOR_ASSET_ID, $flavorAssetId);
		if($isAscending>0)
			$criteria->addAscendingOrderByColumn(mediaInfoPeer::ID);
		else
			$criteria->addDescendingOrderByColumn(mediaInfoPeer::ID);

		return mediaInfoPeer::doSelectOne($criteria);
	}
	
	/**
	 * @param string $entryId
	 * @return mediaInfo
	 */
	public static function retrieveOriginalByEntryId($entryId)
	{
		$sourceFlavorAsset = assetPeer::retrieveOriginalByEntryId($entryId);
		if(!$sourceFlavorAsset)
			return null;
					
		$criteria = new Criteria();
		$criteria->add(mediaInfoPeer::FLAVOR_ASSET_ID, $sourceFlavorAsset->getId());
		$criteria->addDescendingOrderByColumn(mediaInfoPeer::CREATED_AT);

		return mediaInfoPeer::doSelectOne($criteria);
	}

	public static function getCacheInvalidationKeys()
	{
		return array(array("mediaInfo:flavorAssetId=%s", self::FLAVOR_ASSET_ID));		
	}

	/**
	 * @param string $id
	 * @return mediaInfo
	 */
	public static function retrieveById($id)
	{
		$criteria = new Criteria();
		$criteria->add(mediaInfoPeer::ID, $id);
		return mediaInfoPeer::doSelectOne($criteria);
	}
	

}
