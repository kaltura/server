<?php


/**
 * Skeleton subclass for performing query and update operations on the 'cue_point' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.cuePoint
 * @subpackage model
 */
class CuePointPeer extends BaseCuePointPeer 
{
	const MAX_TEXT_LENGTH = 32700;
	const MAX_TAGS_LENGTH = 255;
	
	// the search index column names for additional fields
	const ROOTS = 'cue_point.ROOTS';
	const STR_ENTRY_ID = 'cue_point.STR_ENTRY_ID';
	const STR_CUE_POINT_ID = 'cue_point.STR_CUE_POINT_ID';
	
	// cache classes by their type
	protected static $class_types_cache = array();
	
	/* (non-PHPdoc)
	 * @see BaseCuePointPeer::setDefaultCriteriaFilter()
	 */
	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new Criteria();
		$c->addAnd(CuePointPeer::STATUS, CuePointStatus::DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter($c);
	}
	
	public static function setDefaultCriteriaFilterByKuser()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = self::$s_criteria_filter->getFilter();
		if(!$c)
			$c = new Criteria();
			
		$puserId = kCurrentContext::$ks_uid;
		$partnerId = kCurrentContext::$ks_partner_id;
		if ($puserId && $partnerId)
		{
			$kuserId = kuserPeer::getKuserByPartnerAndUid($partnerId, $puserId);
		    if (! $kuserId) {
				throw new KalturaAPIException ( KalturaErrors::INVALID_USER_ID );
			}
			$c->addAnd(CuePointPeer::KUSER_ID, $kuserId->getId());
		}
		self::$s_criteria_filter->setFilter($c);
	}

	/* (non-PHPdoc)
	 * @see BaseCuePointPeer::getOMClass()
	 */
	public static function getOMClass($row, $colnum)
	{
		if($row)
		{
			$colnum += self::translateFieldName(self::TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$assetType = $row[$colnum];
			if(isset(self::$class_types_cache[$assetType]))
				return self::$class_types_cache[$assetType];
				
			$extendedCls = KalturaPluginManager::getObjectClass(self::OM_CLASS, $assetType);
			if($extendedCls)
			{
				self::$class_types_cache[$assetType] = $extendedCls;
				return $extendedCls;
			}
			self::$class_types_cache[$assetType] = self::OM_CLASS;
		}
			
		return self::OM_CLASS;
	}
	
	/* (non-PHPdoc)
	 * @see BaseCuePointPeer::doSelect()
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
}
