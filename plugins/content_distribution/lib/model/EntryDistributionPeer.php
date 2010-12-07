<?php


/**
 * Skeleton subclass for performing query and update operations on the 'entry_distribution' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class EntryDistributionPeer extends BaseEntryDistributionPeer 
{
	/**
	 * Retrieve objects by entry id.
	 *
	 * @param      string $entryId
	 * @param      PropelPDO $con the connection to use
	 * @return     array<EntryDistribution>
	 */
	public static function retrieveByEntryId($entryId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(EntryDistributionPeer::ENTRY_ID, $entryId);

		return EntryDistributionPeer::doSelect($criteria, $con);
	}
} // EntryDistributionPeer
