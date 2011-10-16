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
		$sourceFlavorAsset = assetPeer::retrieveOriginalByEntryId($entryId);
		if(!$sourceFlavorAsset)
			return null;
					
		$criteria = new Criteria();
		$criteria->add(mediaInfoPeer::FLAVOR_ASSET_ID, $sourceFlavorAsset->getId());

		return mediaInfoPeer::doSelectOne($criteria);
	}

	public static function getCacheInvalidationKeys(Criteria $criteria, $queryType)
	{
		$criterion = $criteria->getCriterion(self::FLAVOR_ASSET_ID);
		if (!$criterion || 
			$criterion->getComparison() != Criteria::EQUAL)
		{
			return array();				
		}
		
		return array("mediaInfo:flavorAssetId=".$criterion->getValue());
	}
}
