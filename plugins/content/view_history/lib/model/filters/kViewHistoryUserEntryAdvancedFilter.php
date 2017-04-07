<?php
/**
 * @package plugins.viewHistory
 * @subpackage model.filters
 */
class kViewHistoryUserEntryAdvancedFilter extends AdvancedSearchFilterItem
{
	/**
	 * @var UserEntryFilter
	 */
	public $filter;
	
	/**
	 * Function to retrieve
	 */
	public function getObjectCount()
	{
		$c = new Criteria();
		$filter->attachToCriteria($criteria);
		
		return UserEntryPeer::doCount($c);
	}
	
	public function getEntryIds()
	{
		$ueCrit = new Criteria();
		$ueCrit->addSelectColumn(UserEntryPeer::ENTRY_ID);
		$this->filter->attachToCriteria($ueCrit);
		
		$stmt = UserEntryPeer::doSelectStmt($ueCrit);
		$ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
		
		return $ids;
	}
	
	public function applyCondition(IKalturaDbQuery $query)
	{
		//get all userEntries
		$entryIds = $this->getEntryIds();
		
		/* @var $query KalturaCriteria */
		if (count($entryIds) <= $query->getLimit())
		{
			KalturaLog::info("Few user entries found - merge with query");
			if($query instanceof IKalturaIndexQuery)
			{
				$entryIdsStr = array();
				foreach($entryIds as $entryId)
				{
					$entryIdsStr[] = '"'.$entryId.'"';
				}
				
				$query->addMatch('@' . entryIndex::getIndexFieldName(entryPeer::ENTRY_ID) . ' (' . implode(' | ', $entryIdsStr) . ')');
			}
			else
			{
				$query->addColumnWhere(entryPeer::ENTRY_ID, $categoryEntries, KalturaCriteria::IN_LIKE);
			}
		}
		
		//if many - run full criteria w/o this filter
		
		//if few entry IDS - search userEntry table w/ entry IDs
	}
}
