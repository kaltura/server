<?php


/**
 * Skeleton subclass for performing query and update operations on the 'search_entry' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.contentDistribution
 * @subpackage model
 */
class SearchEntryPeer extends BaseSearchEntryPeer implements IMySqlSearchPeer 
{
	/* (non-PHPdoc)
	 * @see IMySqlSearchPeer::getPrimaryKeyField()
	 */
	public static function getPrimaryKeyField()
	{
		return entryPeer::ID;
	}
	
	/* (non-PHPdoc)
	 * @see IMySqlSearchPeer::getSearchPrimaryKeyField()
	 */
	public static function getSearchPrimaryKeyField()
	{
		return self::ENTRY_ID;
	}
	
	/* (non-PHPdoc)
	 * @see IMySqlSearchPeer::doCountOnSourceTable()
	 */
	public static function doCountOnSourceTable(Criteria $criteria, $distinct = false, PropelPDO $con = null)
	{
		return entryPeer::doCount($criteria, $distinct, $con);
	}
} // SearchEntryPeer
