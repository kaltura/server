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
	public static function retrieveEntryIdAndCatalogItemId($entryId, $catalogItemId, $partnerId)
	{
		$c = new Criteria();
		$c->add(EntryVendorTaskPeer::ENTRY_ID, $entryId);
		$c->add(EntryVendorTaskPeer::CATALOG_ITEM_ID, $catalogItemId);
		$c->add(EntryVendorTaskPeer::PARTNER_ID, $partnerId);
		
		return EntryVendorTaskPeer::doSelectOne($c);
	}
	
	public static function retrievePendingByEntryId($entryId, $partnerId = null)
	{
		$c = new Criteria();
		$c->add(EntryVendorTaskPeer::ENTRY_ID, $entryId);
		$c->add(EntryVendorTaskPeer::STATUS, array(EntryVendorTaskStatus::PENDING, EntryVendorTaskStatus::PENDING_MODERATION), Criteria::IN);
		if ($partnerId)
			$c->add(EntryVendorTaskPeer::PARTNER_ID,$partnerId );
		return EntryVendorTaskPeer::doSelect($c);
	}

	public static function retrievePendingByEntryIdAndPartnerId($entryId, $partnerId = null)
	{
		return self::retrievePendingByEntryId($entryId, $partnerId);
	}
	
	public static function retrieveByPKAndVendorPartnerId($taskId, $partnerId)
	{
		$c = new Criteria();
		$c->add(EntryVendorTaskPeer::ID, $taskId);
		$c->add(EntryVendorTaskPeer::VENDOR_PARTNER_ID, $partnerId);
		
		return EntryVendorTaskPeer::doSelectOne($c);
	}
	
	
} // EntryVendorTaskPeer
