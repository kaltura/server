<?php


/**
 * Skeleton subclass for performing query and update operations on the 'user_entry' table.
 *
 * Describes the relationship between a specific user and a specific entry
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class UserEntryPeer extends BaseUserEntryPeer {

	// cache classes by their type
	protected static $class_types_cache = array(
	);

	/* (non-PHPdoc)
	 * @see BaseUserEntryPeer::setDefaultCriteriaFilter()
	 */
	public static function setDefaultCriteriaFilter()
	{
	    if(self::$s_criteria_filter == null)
	        self::$s_criteria_filter = new criteriaFilter();
	    
	    $c = KalturaCriteria::create(UserEntryPeer::OM_CLASS);
	    $c->addAnd ( UserEntryPeer::STATUS, array(UserEntryStatus::DELETED), Criteria::NOT_IN);
	    // when session is not admin, allow access to user's userEntries only
	    if (kCurrentContext::$ks && !kCurrentContext::$is_admin_session) {
    	    $c->addAnd(UserEntryPeer::KUSER_ID, kCurrentContext::getCurrentKsKuserId());
	    }

        $partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
        $c->addAnd (UserEntryPeer::PARTNER_ID,$partnerId);

	    self::$s_criteria_filter->setFilter($c);
	}
	
	public static function getOMClass($row, $colnum)
	{
		if($row)
		{
			$typeField = self::translateFieldName(UserEntryPeer::TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$userEntryType = $row[$typeField];
			if(isset(self::$class_types_cache[$userEntryType]))
				return self::$class_types_cache[$userEntryType];
			$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $userEntryType);
			if($extendedCls)
			{
				self::$class_types_cache[$userEntryType] = $extendedCls;
				return $extendedCls;
			}
			self::$class_types_cache[$userEntryType] = parent::OM_CLASS;
		}
		return parent::OM_CLASS;
	}
	
	public static function getEntryIdsByFilter($limit, $offset = 0, $filter = null, $entryIds = array())
	{
		$userEntryCriteria = new Criteria();
		
		$userEntryCriteria->addSelectColumn(self::ENTRY_ID);
		if($filter)
			$filter->attachToCriteria($userEntryCriteria);
		
		if(count ($entryIds))
		{
			$userEntryCriteria->add(self::ENTRY_ID, $entryIds, Criteria::IN);
		}
		
		$userEntryCriteria->add(self::PARTNER_ID, kCurrentContext::$ks_partner_id);
		$userEntryCriteria->setLimit($limit);
		$userEntryCriteria->setOffset($offset);
		
		$stmt = self::doSelectStmt($userEntryCriteria);
		$ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
		
		return $ids;
	}
	
	public static function getUserEntry ($partnerId, $userId, $entryId, $type)
	{
		$userEntryCriteria = new Criteria();
		$userEntryCriteria->add(self::PARTNER_ID, $partnerId);
		$userEntryCriteria->add(self::ENTRY_ID, $entryId);
		$userEntryCriteria->add(self::KUSER_ID, $userId);
		$userEntryCriteria->add(self::TYPE, $type);
		
		return self::doSelectOne($userEntryCriteria);
	}

} // UserEntryPeer
