<?php


/**
 * Skeleton subclass for performing query and update operations on the 'category_entry' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class categoryEntryPeer extends BasecategoryEntryPeer {

	public static function retrieveByCategoryIdAndEntryId($categoryId, $entryId)
	{
		$c = new Criteria();
		$c->add(self::PARTNER_ID, kCurrentContext::$ks_partner_id);
		$c->add(self::CATEGORY_ID, $categoryId);
		$c->add(self::ENTRY_ID, $entryId);
		
		return self::doSelectOne($c);
	}
} // categoryEntryPeer
