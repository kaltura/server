<?php

/**
 * Subclass for performing query and update operations on the 'flavor_params' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class assetParamsPeer extends BaseassetParamsPeer
{
	const FLAVOR_OM_CLASS = 'flavorParams';
	const THUMBNAIL_OM_CLASS = 'thumbParams';
	const LIVE_OM_CLASS = 'liveParams';
	
	static protected $filterPartner = null;
	
	static protected $isDefaultInDefaultCriteria = true;

	/**
	 * Define if is_default needed in the default criteria for partner zero
	 * @param bool $is
	 */
	public static function setIsDefaultInDefaultCriteria($is)
	{
		self::$isDefaultInDefaultCriteria = $is;
	}
	
	public static function setDefaultCriteriaFilter ()
	{
		if(is_null(self::$s_criteria_filter))
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new Criteria(); 
		$c->add(self::DELETED_AT, null, Criteria::ISNULL);
		self::$s_criteria_filter->setFilter($c);
	}
	
	// cache classes by their type
	protected static $class_types_cache = array(
		assetType::FLAVOR => assetParamsPeer::FLAVOR_OM_CLASS,
		assetType::THUMBNAIL => assetParamsPeer::THUMBNAIL_OM_CLASS,
		assetType::LIVE => assetParamsPeer::LIVE_OM_CLASS,
	);

	public static function excludeId($id)
	{
		$criteriaFilter = self::getCriteriaFilter();
		$criteria = $criteriaFilter->getFilter();
		$criteria->addAnd(self::ID, $id, Criteria::NOT_EQUAL);
	}

	public static function addPartnerToCriteria($partnerId, $privatePartnerData = false, $partnerGroup = null, $kalturaNetwork = null)
	{
		self::$filterPartner = $partnerId;
		
		if(!self::$isDefaultInDefaultCriteria)
			return parent::addPartnerToCriteria($partnerId, $privatePartnerData, $partnerGroup, $kalturaNetwork);
		
		$criteriaFilter = self::getCriteriaFilter();
		$criteria = $criteriaFilter->getFilter();
		
		if(!$privatePartnerData)
		{
			// the private partner data is not allowed - 
			if($kalturaNetwork)
			{
				// allow only the kaltura network stuff
				if($partnerId)
				{
					$orderBy = "(" . self::PARTNER_ID . "<>{$partnerId})";  // first take the pattner_id and then the rest
					myCriteria::addComment($criteria , "Only Kaltura Network");
					$criteria->addAscendingOrderByColumn($orderBy);//, Criteria::CUSTOM );
				}
			}
			else
			{
				// no private data and no kaltura_network - 
				// add a criteria that will return nothing
				$criteria->addAnd(self::PARTNER_ID, Partner::PARTNER_THAT_DOWS_NOT_EXIST);
			}
		}
		else
		{
			// private data is allowed
			if(empty($partnerGroup) && empty($kalturaNetwork))
			{
				// the default case
				$criteria->addAnd(self::PARTNER_ID, $partnerId);
			}
			elseif ($partnerGroup == myPartnerUtils::ALL_PARTNERS_WILD_CHAR)
			{
				// all is allowed - don't add anything to the criteria
			}
			else 
			{
				$criterion = null;
				if($partnerGroup)
				{
					// $partnerGroup hold a list of partners separated by ',' or $kalturaNetwork is not empty (should be mySearchUtils::KALTURA_NETWORK = 'kn')
					$partners = explode(',', trim($partnerGroup));
					$hasPartnerZero = false;
					foreach($partners as $index => &$p)
					{
						trim($p); // make sure there are not leading or trailing spaces
						if($p == 0)
						{
							unset($partners[$index]);
							$hasPartnerZero = true;
						}
					}
	
					// add the partner_id to the partner_group
					$partners[] = strval($partnerId);
					
					$criterion = $criteria->getNewCriterion(self::PARTNER_ID, $partners, Criteria::IN);
					
					if($hasPartnerZero)
					{
						$defaultAndGlobal = $criteria->getNewCriterion(self::PARTNER_ID, 0);
						$defaultAndGlobal->addAnd($criteria->getNewCriterion(self::IS_DEFAULT, 1));
						$criterion->addOr($defaultAndGlobal);
					}
				}
				else 
				{
					$criterion = $criteria->getNewCriterion(self::PARTNER_ID, $partnerId);
				}	
				
				$criteria->addAnd($criterion);
			}
		}
			
		$criteriaFilter->enable();
	}
	
	/**
	 * The returned Class will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @param      array $row PropelPDO result row.
	 * @param      int $colnum Column to examine for OM class information (first is 0).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getOMClass($row, $colnum)
	{
		if($row)
		{
			$typeField = self::translateFieldName(assetParamsPeer::TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$assetType = $row[$typeField];
			if(isset(self::$class_types_cache[$assetType]))
				return self::$class_types_cache[$assetType];
				
			$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $assetType);
			if($extendedCls)
			{
				self::$class_types_cache[$assetType] = $extendedCls;
				return $extendedCls;
			}
			self::$class_types_cache[$assetType] = parent::OM_CLASS;
		}
			
		return parent::OM_CLASS;
	}

	protected static function filterSelectResultsCallback($obj)
	{
		$requiredPermissions = $obj->getRequiredPermissions();
		if(!$requiredPermissions || !count($requiredPermissions))
		{
			return true;
		}
		
		foreach($requiredPermissions as $requiredPermission)
		{
			if(!PermissionPeer::isValidForPartner($requiredPermission, self::$filterPartner))
			{
				self::excludeId($obj->getId());
				return false; 
			}
		}
		return true;
	}
	
	public static function filterSelectResults(&$selectResults, Criteria $criteria)
	{
		$criteria_filter = assetParamsPeer::getCriteriaFilter();
		if(!$criteria_filter->isEnabled() || !self::$filterPartner)
		{
			return;
		}
		
		$selectResults = array_filter(
			$selectResults, 
			array('assetParamsPeer', 'filterSelectResultsCallback'));
	}
		
	public static function retrieveByPKNoFilter ($pk, $con = null)
	{
		self::setUseCriteriaFilter ( false );
		$res = parent::retrieveByPK( $pk , $con );
		self::setUseCriteriaFilter ( true );
		return $res;
	}

	public static function retrieveByPKsNoFilter ($pks, $con = null)
	{
		self::setUseCriteriaFilter ( false );
		$res = parent::retrieveByPKs( $pks , $con );
		self::setUseCriteriaFilter ( true );
		return $res;
	}
	
	/**
	 * @param int $conversionProfileId
	 * @param string $tag
	 * @param $con
	 * @return array<flavorParamsOutput>
	 */
	public static function retrieveByProfileAndTag($conversionProfileId, $tag, $con = null)
	{
		$flavorIds = flavorParamsConversionProfilePeer::getFlavorIdsByProfileId($conversionProfileId);
		
		$criteria = new Criteria();
		$criteria->add(assetParamsPeer::ID, $flavorIds, Criteria::IN);

		$flavorParams = assetParamsPeer::doSelect($criteria, $con);
		
		$ret = array();
		
		foreach($flavorParams as $flavorParamsItem)
			if($flavorParamsItem->hasTag($tag))
				$ret[] = $flavorParamsItem;
		
		return $ret;
	}
	
	/**
	 * @param int $conversionProfileId
	 * @param $con
	 * @return array<flavorParamsOutput>
	 */
	public static function retrieveByProfile($conversionProfileId, $con = null)
	{
		$flavorIds = flavorParamsConversionProfilePeer::getFlavorIdsByProfileId($conversionProfileId);
		
		$criteria = new Criteria();
		$criteria->add(assetParamsPeer::ID, $flavorIds, Criteria::IN);

		return assetParamsPeer::doSelect($criteria, $con);
	}

	public static function retrieveFlavorsByPKs($pks, PropelPDO $con = null)
	{
		$criteria = new Criteria(assetParamsPeer::DATABASE_NAME);
		$criteria->add(assetParamsPeer::ID, $pks, Criteria::IN);
		
		$types = self::retrieveAllFlavorParamsTypes();
		$criteria->add(assetParamsPeer::TYPE, $types, Criteria::IN);
		
		return assetParamsPeer::doSelect($criteria, $con);
	}

	public static function retrieveThumbnailsByPKs($pks, PropelPDO $con = null)
	{
		$criteria = new Criteria(assetParamsPeer::DATABASE_NAME);
		$criteria->add(assetParamsPeer::ID, $pks, Criteria::IN);
		
		$types = KalturaPluginManager::getExtendedTypes(assetParamsPeer::OM_CLASS, assetType::THUMBNAIL);
		$criteria->add(assetParamsPeer::TYPE, $types, Criteria::IN);
		
		return assetParamsPeer::doSelect($criteria, $con);
	}
	
	public static function retrieveMinimalBitrate($pks, PropelPDO $con = null) 
	{
		$criteria = new Criteria(assetParamsPeer::DATABASE_NAME);
		$criteria->add(assetParamsPeer::ID, $pks, Criteria::IN);
		$criteria->add(assetParamsPeer::VIDEO_BITRATE, 0, Criteria::GREATER_THAN);
		
		$notAudioCond = $criteria->getNewCriterion(assetParamsPeer::WIDTH, 0, Criteria::GREATER_THAN);
		$notAudioCond->addOr($criteria->getNewCriterion(assetParamsPeer::HEIGHT, 0, Criteria::GREATER_THAN));
		
		$criteria->add($notAudioCond);
		$criteria->addAscendingOrderByColumn(assetParamsPeer::VIDEO_BITRATE);
		
		return assetParamsPeer::doSelectOne($criteria, $con);
	}

	public static function getIds(Criteria $criteria, $con = null)
	{
		$criteria->addSelectColumn(assetParamsPeer::ID);

		$stmt = assetParamsPeer::doSelectStmt($criteria, $con);
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("flavorParams:id=%s", self::ID), array("flavorParams:partnerId=%s", self::PARTNER_ID));		
	}
	
	public static function retrieveBySystemName ($v, $con = null)
	{
		$c = new Criteria();
		$c->addAnd(assetParamsPeer::SYSTEM_NAME, $v);
		
		return self::doSelectOne($c, $con);
	}

	public static function retrieveAllFlavorParamsTypes(){
		$flavorTypes = KalturaPluginManager::getExtendedTypes(self::OM_CLASS, assetType::FLAVOR);
		$flavorTypes[] = assetType::LIVE;
		return $flavorTypes;
		
	}
}
