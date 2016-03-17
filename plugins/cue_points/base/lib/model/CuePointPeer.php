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
class CuePointPeer extends BaseCuePointPeer implements IMetadataPeer, IRelatedObjectPeer 
{
	const MAX_TEXT_LENGTH = 32700;
	const MAX_TAGS_LENGTH = 255;
	
	// the search index column names for additional fields
	const ROOTS = 'cue_point.ROOTS';
	const STR_ENTRY_ID = 'cue_point.STR_ENTRY_ID';
	const STR_CUE_POINT_ID = 'cue_point.STR_CUE_POINT_ID';
	const FORCE_STOP = 'cue_point.FORCE_STOP';
	const DURATION = 'cue_point.DURATION';
	const IS_PUBLIC = 'cue_point.IS_PUBLIC';
	
	// cache classes by their type
	protected static $class_types_cache = array();
	
	private static $userContentOnly = false;
	
	public static function setUserContentOnly($contentOnly)
	{
		self::$userContentOnly = $contentOnly;
	}

	/* (non-PHPdoc)
	 * @see BaseCuePointPeer::setDefaultCriteriaFilter()
	 */
	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = KalturaCriteria::create(CuePointPeer::OM_CLASS);
		$c->addAnd(CuePointPeer::STATUS, CuePointStatus::DELETED, Criteria::NOT_EQUAL);
		
		if(self::$userContentOnly)
		{
			$puserId = kCurrentContext::$ks_uid;
			$partnerId = kCurrentContext::$ks_partner_id;
			
			if ($puserId && $partnerId)
			{
				$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $puserId);
				if (! $kuser) {
					$kuser = kuserPeer::createKuserForPartner($partnerId, $puserId);
				}
			
				// Temporarily change user filter to (user==kuser OR cuepoint of type THUMB/CODE). Long term fix will be accomplished
				// by adding a public property on the cuepoint object and checking (user==kuser OR is public)
				//$c->addAnd(CuePointPeer::KUSER_ID, $kuser->getId());
				$criterionUserOrPublic = $c->getNewCriterion(CuePointPeer::KUSER_ID, $kuser->getId());
				$criterionUserOrPublic->addOr($c->getNewCriterion (self::IS_PUBLIC, true, Criteria::EQUAL));
				$criterionUserOrPublic->addTag(KalturaCriterion::TAG_USER_SESSION);
				$criterionUserOrPublic->addOr(
						$c->getNewCriterion(
								CuePointPeer::TYPE,
								array(
										ThumbCuePointPlugin::getCuePointTypeCoreValue(ThumbCuePointType::THUMB),
										CodeCuePointPlugin::getCuePointTypeCoreValue(CodeCuePointType::CODE),
										AdCuePointPlugin::getCuePointTypeCoreValue(AdCuePointType::AD),
								),
								Criteria::IN
						)
				);
			
				$c->addAnd($criterionUserOrPublic);
			}
			else if (!$puserId)
			{
				$criterionIsPublic = $c->getNewCriterion (self::IS_PUBLIC, true, Criteria::EQUAL);
				$criterionIsPublic->addTag(KalturaCriterion::TAG_WIDGET_SESSION);
				$c->add($criterionIsPublic);					
 			}
		}
		
		self::$s_criteria_filter->setFilter($c);
	}

	/* (non-PHPdoc)
	 * @see BaseCuePointPeer::getOMClass()
	 */
	public static function getOMClass($row, $colnum)
	{
		$assetType = null;
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
		}
			
		throw new Exception("Can't instantiate un-typed [$assetType] cue point [" . print_r($row, true) . "]");
	}
	
	/**
	 * Override in order to filter objects returned from doSelect.
	 *
	 * @param      array $selectResults The array of objects to filter.
	 * @param          Criteria $criteria
	 */
	public static function filterSelectResults(&$selectResults, Criteria $criteria)
	{
		if(!empty($selectResults) && self::$userContentOnly)
		{
			$removedRecordsCount = 0;
			foreach ($selectResults as $key => $cuePoint)
			{
				/* @var $cuePoint CuePoint */
				if(kCurrentContext::$ks_uid && $cuePoint->getPuserId() !== kCurrentContext::$ks_uid && !$cuePoint->getIsPublic())
				{
					unset($selectResults[$key]);
					$removedRecordsCount++;
				}
			}
		
			if($criteria instanceof KalturaCriteria)
			{
				$recordsCount = $criteria->getRecordsCount();
				$criteria->setRecordsCount($recordsCount - $removedRecordsCount);
			}
		}
	
		parent::filterSelectResults($selectResults, $criteria);
	}
	
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{
		KalturaCriterion::disableTags(array(KalturaCriterion::TAG_USER_SESSION,KalturaCriterion::TAG_WIDGET_SESSION));
		$res = parent::retrieveByPK($pk, $con);
		KalturaCriterion::restoreTags(array(KalturaCriterion::TAG_USER_SESSION,KalturaCriterion::TAG_WIDGET_SESSION));

		return $res;
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

	/**
	 * Retrieve a single object by system name.
	 * The cue point system name is unique per entry
	 *
	 * @param      string $entryId the entry id.
	 * @param      string $systemName the system name.
	 * @param      PropelPDO $con the connection to use
	 * @return     CuePoint
	 */
	public static function retrieveBySystemName($entryId, $systemName, PropelPDO $con = null)
	{
		$criteria = KalturaCriteria::create(CuePointPeer::OM_CLASS);
		$criteria->add(CuePointPeer::ENTRY_ID, $entryId);
		$criteria->add(CuePointPeer::SYSTEM_NAME, $systemName);

		return CuePointPeer::doSelectOne($criteria, $con);
	}

	/**
	 * Retrieve multiple objects by entry id.
	 *
	 * @param      string $entryId the entry id.
	 * @param      array $types the cue point types from CuePointType enum
	 * @param      PropelPDO $con the connection to use
	 * @return     CuePoint
	 */
	public static function retrieveByEntryId($entryId, $types = null, PropelPDO $con = null)
	{
		$criteria = KalturaCriteria::create(CuePointPeer::OM_CLASS);
		$criteria->add(CuePointPeer::ENTRY_ID, $entryId);
		$criteria->add(CuePointPeer::STATUS, CuePointStatus::DELETED, Criteria::NOT_EQUAL);
		
		if(!is_null($types))
			$criteria->add(CuePointPeer::TYPE, $types, Criteria::IN);
		$criteria->addAscendingOrderByColumn(CuePointPeer::START_TIME);
		return CuePointPeer::doSelect($criteria, $con);
	}
	
	
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("cuePoint:id=%s", self::ID), array("cuePoint:entryId=%s", self::ENTRY_ID));		
	}
	
	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      string $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     CuePoint
	 */
	public static function retrieveByPKNoFilter($pk, PropelPDO $con = null)
	{
		self::setUseCriteriaFilter ( false );
		$res = self::retrieveByPK($pk, $con);
		self::setUseCriteriaFilter ( true );
		return $res;
	}
	
	public static function validateMetadataObjects($profileField, $objectIds, &$errorMessage)
	{
	    return true;
	}

	public static function getEntry($objectId)
	{
		$cuePoint = self::retrieveByPK($objectId);
		if (!$cuePoint)
			return null;
		return $cuePoint ? entryPeer::retrieveByPK($cuePoint->getEntryId()) : null;
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getRootObjects()
	 */
	public function getRootObjects(IRelatedObject $object)
	{
		/* @var $object CuePoint */
		$entry = entryPeer::retrieveByPK($object->getEntryId());
		if($entry)
			return array($entry);
		
		return array();
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::isReferenced()
	 */
	public function isReferenced(IRelatedObject $object)
	{
		return false;
	}
}
