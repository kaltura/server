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

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('PartnerDescription' => 0, 'Language' => 1, 'Label' => 2, 'Status' => 3, 'Size' => 4, 'UpdatedAt' => 5),
		BasePeer::TYPE_STUDLYPHPNAME => array ('partnerDescription' => 0, 'language' => 1, 'label' => 2, 'status' => 3, 'size' => 4, 'updated_at' => 5),
		BasePeer::TYPE_COLNAME => array (self::PARTNER_DESCRIPTION => 0, self::LANGUAGE => 1, self::LABEL => 2, self::STATUS => 3, self::SIZE => 4, self::UPDATED_AT => 5),
		BasePeer::TYPE_FIELDNAME => array ('partner_description' => 0, 'language' => 1, 'label' => 2, 'status' => 3, 'size' => 4, 'updated_at' => 5),
		BasePeer::TYPE_NUM => array (0, 1, 2)
	);
	
	public static function translateFieldName($name, $fromType, $toType)
	{
		$toNames = self::getFieldNames($toType);
		$key = isset(self::$fieldKeys[$fromType][$name]) ? self::$fieldKeys[$fromType][$name] : null;
		if ($key === null)
			return parent::translateFieldName($name, $fromType, $toType);
			
		return $toNames[$key];
	}

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
