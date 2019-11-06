<?php
/**
 * Subclass for performing query and update operations on the 'kvote' table.
 *
 *
 *
 * @package Core
 * @subpackage model
 */

class kRatingPeer extends kvotePeer
{
	public static function countEntryKvotesByRank ($entryId, $rankValues)
	{
		$partnerId = kCurrentContext::getCurrentPartnerId();
		
		$criteria = new Criteria();
		$criteria->add(kvotePeer::PARTNER_ID, $partnerId);
		$criteria->add(kvotePeer::ENTRY_ID, $entryId);
		$criteria->add(kvotePeer::RANK, $rankValues, Criteria::IN);
		$criteria->addGroupByColumn(kvotePeer::RANK);
		$criteria->addSelectColumn(kvotePeer::ENTRY_ID);
		$criteria->addSelectColumn(kvotePeer::RANK);
		$criteria->addSelectColumn('COUNT(' . kvotePeer::ID . ')');
		
		$stmt = kvotePeer::doSelectStmt($criteria);
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$result = array();
		foreach ($rows as $row)
		{
			$ratingCount = new RatingCount();
			$ratingCount->setEntryId($row[kvotePeer::ENTRY_ID]);
			$ratingCount->setRank($row[kvotePeer::RANK]);
			$ratingCount->setCount('COUNT(' . kvotePeer::ID . ')');
			
			$result[] = $ratingCount;
		}
		
		return $result;
	}
}