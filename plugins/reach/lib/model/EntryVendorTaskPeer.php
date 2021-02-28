<?php


/**
 * Skeleton subclass for performing query and update operations on the 'entry_vendor_task' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.reach
 * @subpackage model
 */
class EntryVendorTaskPeer extends BaseEntryVendorTaskPeer 
{
	public static function setDefaultCriteriaFilter ()
	{
		if(is_null(self::$s_criteria_filter))
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new Criteria();
		$c->add(self::STATUS, EntryVendorTaskStatus::ABORTED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter($c);
	}
	
	public static function retrieveEntryIdAndCatalogItemIdAndEntryVersion($entryId, $catalogItemId, $partnerId, $version)
	{
		$c = new Criteria();
		$c->add(EntryVendorTaskPeer::ENTRY_ID, $entryId);
		$c->add(EntryVendorTaskPeer::CATALOG_ITEM_ID, $catalogItemId);
		$c->add(EntryVendorTaskPeer::PARTNER_ID, $partnerId);
		$c->add(EntryVendorTaskPeer::VERSION, $version);
		return EntryVendorTaskPeer::doSelect($c);
	}
	
	public static function retrieveActiveTasks($entryId, $catalogItemId, $partnerId, $version, $statuses = array())
	{
		$c = new Criteria();
		$c->add(EntryVendorTaskPeer::ENTRY_ID, $entryId);
		$c->add(EntryVendorTaskPeer::CATALOG_ITEM_ID, $catalogItemId);
		$c->add(EntryVendorTaskPeer::PARTNER_ID, $partnerId);
		if ($version)
		{
			$c->add(EntryVendorTaskPeer::VERSION, $version);
		}
		if ($statuses)
		{
			$c->add(EntryVendorTaskPeer::STATUS, $statuses, Criteria::IN);
		}
		else
		{
			$c->add(EntryVendorTaskPeer::STATUS,
				array(EntryVendorTaskStatus::PROCESSING,
					EntryVendorTaskStatus::READY,
					EntryVendorTaskStatus::PENDING,
					EntryVendorTaskStatus::PENDING_MODERATION,
					EntryVendorTaskStatus::PENDING_ENTRY_READY
				), Criteria::IN);
		}
		$c->addDescendingOrderByColumn(EntryVendorTaskPeer::VERSION);
		$c->addDescendingOrderByColumn(EntryVendorTaskPeer::ID);
		return EntryVendorTaskPeer::doSelectOne($c);
	}
	
	public static function retrievePendingByEntryId($entryId, $partnerId = null ,$status = array(EntryVendorTaskStatus::PENDING, EntryVendorTaskStatus::PENDING_MODERATION, EntryVendorTaskStatus::PENDING_ENTRY_READY))
	{
		$c = new Criteria();
		$c->add(EntryVendorTaskPeer::ENTRY_ID, $entryId);
		$c->add(EntryVendorTaskPeer::STATUS, $status, Criteria::IN);
		if ($partnerId)
			$c->add(EntryVendorTaskPeer::PARTNER_ID,$partnerId );
		return EntryVendorTaskPeer::doSelect($c);
	}
	
	public static function retrieveByEntryIdAndStatuses($entryId, $partnerId = null ,$statuses)
	{
		$c = new Criteria();
		$c->add(EntryVendorTaskPeer::ENTRY_ID, $entryId);
		$c->add(EntryVendorTaskPeer::STATUS, $statuses, Criteria::IN);
		if ($partnerId)
			$c->add(EntryVendorTaskPeer::PARTNER_ID, $partnerId);
		
		return EntryVendorTaskPeer::doSelect($c);
	}
	
	public static function retrieveByPKAndVendorPartnerId($taskId, $partnerId)
	{
		$c = new Criteria();
		$c->add(EntryVendorTaskPeer::ID, $taskId);
		$c->add(EntryVendorTaskPeer::VENDOR_PARTNER_ID, $partnerId);
		
		return EntryVendorTaskPeer::doSelectOne($c);
	}
	
	public static function retrieveExistingTasksCatalogItemIds($entryId, $catalogItemIds)
	{
		$c = new Criteria();
		$c->add(EntryVendorTaskPeer::ENTRY_ID, $entryId);
		$c->add(EntryVendorTaskPeer::CATALOG_ITEM_ID, $catalogItemIds, Criteria::IN);
		$c->addGroupByColumn(EntryVendorTaskPeer::CATALOG_ITEM_ID);
		$c->addSelectColumn(EntryVendorTaskPeer::CATALOG_ITEM_ID);
		
		$stmt = EntryVendorTaskPeer::doSelectStmt($c, null);
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}
	
	/* (non-PHPdoc)
 	 * @see BaseEntryVendorTaskPeer::doSelect()
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
	
} // EntryVendorTaskPeer
