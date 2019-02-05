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

		$ks = kCurrentContext::$ks_object;
		$privilagedEntryIdEdit = null;
		if ($ks)
		{
			$valusEdit = $ks->getPrivilegeValues(ks::PRIVILEGE_EDIT);
			if ($valusEdit && count($valusEdit) > 0)
				$privilagedEntryIdEdit = $valusEdit[0];

			if ($privilagedEntryIdEdit)
			{
				$c->addAnd(UserEntryPeer::ENTRY_ID, $privilagedEntryIdEdit);
			}

			// when session is not admin and not co-editor, allow access to user's userEntries only
			if (!kCurrentContext::$is_admin_session && !$privilagedEntryIdEdit)
			{
				$c->addAnd(UserEntryPeer::KUSER_ID, kCurrentContext::getCurrentKsKuserId());
			}
		}
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
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("userEntry:kuserId=%s", self::KUSER_ID));		
	}
} // UserEntryPeer
