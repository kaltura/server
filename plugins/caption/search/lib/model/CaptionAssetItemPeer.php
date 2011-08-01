<?php
/**
 * @package plugins.captionSearch
 * @subpackage model
 */ 
class CaptionAssetItemPeer extends assetPeer
{
	const OM_CLASS = 'CaptionAssetItem';
	
	const ID = 'flavor_asset.ID';
	const INT_ID = 'flavor_asset.INT_ID';
	const PARTNER_ID = 'flavor_asset.PARTNER_ID';
	const TAGS = 'flavor_asset.TAGS';
	const CREATED_AT = 'flavor_asset.CREATED_AT';
	const UPDATED_AT = 'flavor_asset.UPDATED_AT';
	const DELETED_AT = 'flavor_asset.DELETED_AT';
	const ENTRY_ID = 'flavor_asset.ENTRY_ID';
	const FLAVOR_PARAMS_ID = 'flavor_asset.FLAVOR_PARAMS_ID';
	const STATUS = 'flavor_asset.STATUS';
	const VERSION = 'flavor_asset.VERSION';
	const DESCRIPTION = 'flavor_asset.DESCRIPTION';
	const SIZE = 'flavor_asset.SIZE';
	const CONTAINER_FORMAT = 'flavor_asset.CONTAINER_FORMAT';
	
	const CONTENT = 'caption_asset_item.CONTENT';
	const PARTNER_DESCRIPTION = 'caption_asset_item.PARTNER_DESCRIPTION';
	const LANGUAGE = 'caption_asset_item.LANGUAGE';
	const LABEL = 'caption_asset_item.LABEL';
	const START_TIME = 'caption_asset_item.START_TIME';
	const END_TIME = 'caption_asset_item.END_TIME';

	
	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Content' => 0, 'PartnerDescription' => 1, 'Language' => 2, 'Label' => 3, 'StartTime' => 4, 'EndTime' => 5),
		BasePeer::TYPE_STUDLYPHPNAME => array ('content' => 0, 'partnerDescription' => 1, 'language' => 2, 'label' => 3, 'startTime' => 4, 'endTime' => 5),
		BasePeer::TYPE_COLNAME => array (self::CONTENT => 0, self::PARTNER_DESCRIPTION => 1, self::LANGUAGE => 2, self::LABEL => 3, self::START_TIME => 4, self::END_TIME => 5),
		BasePeer::TYPE_FIELDNAME => array ('content' => 0, 'partner_description' => 1, 'language' => 2, 'label' => 3, 'start_time' => 4, 'end_time' => 5),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5)
	);
	
	public static function translateFieldName($name, $fromType, $toType)
	{
		$toNames = self::getFieldNames($toType);
		$key = isset(self::$fieldKeys[$fromType][$name]) ? self::$fieldKeys[$fromType][$name] : null;
		if ($key === null)
			return assetPeer::translateFieldName($name, $fromType, $toType);
			
		return $toNames[$key];
	}
	
	/* (non-PHPdoc)
	 * @see BaseassetPeer::doSelect()
	 */
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		if(!($criteria instanceof KalturaCriteria) || !($criteria instanceof ICaptionAssetItemCriteria))
			return array();
			
		return $criteria->getCaptionAssetItems();
	}
}