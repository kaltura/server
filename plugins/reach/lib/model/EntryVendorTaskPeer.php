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
	
} // EntryVendorTaskPeer
