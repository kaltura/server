<?php


/**
 * Skeleton subclass for performing query and update operations on the 'caption_asset_item' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.captionSearch
 * @subpackage model
 */
class CaptionAssetItemPeer extends BaseCaptionAssetItemPeer {

	const TAGS = 'caption_asset_item.TAGS';
	const PARTNER_DESCRIPTION = 'caption_asset_item.PARTNER_DESCRIPTION';
	const LANGUAGE = 'caption_asset_item.LANGUAGE';
	const LABEL = 'caption_asset_item.LABEL';
	const STATUS = 'caption_asset_item.STATUS';
	const SIZE = 'caption_asset_item.SIZE';
	const UPDATED_AT = 'caption_asset_item.UPDATED_AT';
	const FORMAT = 'caption_asset_item.FORMAT';

	/**
	 * @param Criteria $criteria
	 * @param PropelPDO $con
	 */
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		$c = clone $criteria;
		
		if($c instanceof KalturaCriteria)
		{
			$c->applyFilters();
			$criteria->setRecordsCount($c->getRecordsCount());
		}
			
		return parent::doSelect($c, $con);
	}

	public static function retrieveByAssetId($assetId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(CaptionAssetItemPeer::CAPTION_ASSET_ID, $assetId);

		return CaptionAssetItemPeer::doSelect($criteria, $con);
	}
	
} // CaptionAssetItemPeer
