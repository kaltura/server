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
	const ENTRY_ID_COLUMN_NAME = 'ENTRY_ID';
	
	const RANK_ID_COLUMN_NAME = 'RANK';
	
	public static function countEntryKvotesByRank ($entryId, $rankValues)
	{
		$partnerId = kCurrentContext::getCurrentPartnerId();
		
		$criteria = new Criteria();
		$criteria->add(self::PARTNER_ID, $partnerId);
		$criteria->add(self::ENTRY_ID, $entryId);
		$criteria->add(self::RANK, $rankValues, Criteria::IN);
		$criteria->addGroupByColumn(self::RANK);
		$criteria->addSelectColumn(self::ENTRY_ID);
		$criteria->addSelectColumn(self::RANK);
		$criteria->addSelectColumn('COUNT(' . self::ID . ')');
		
		$stmt = self::doSelectStmt($criteria);
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$result = array();
		foreach ($rows as $row)
		{
			$ratingCount = new RatingCount();
			$ratingCount->setEntryId($row[self::ENTRY_ID_COLUMN_NAME]);
			$ratingCount->setRank($row[self::RANK_ID_COLUMN_NAME]);
			$ratingCount->setCount($row['COUNT(' . self::ID . ')']);
			
			$result[] = $ratingCount;
		}
		
		return $result;
	}
}