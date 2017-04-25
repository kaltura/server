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
	
	protected $disable;
	
	/**
	 * Function to retrieve
	 */
	public function getObjectCount()
	{
		$c = new Criteria();
		$filter->attachToCriteria($criteria);
		
		return UserEntryPeer::doCount($c);
	}
	
	public function getEntryIds($entryIds = array())
	{
		$ueCrit = new Criteria();
		
		if (kEntitlementUtils::getEntitlementEnforcement())
		{
			$privacyContexts = kEntitlementUtils::getKsPrivacyContextArray();
			$ueCrit->addAnd(self::PRIVACY_CONTEXT, $privacyContexts, Criteria::IN);
		}
		
		$ueCrit->addSelectColumn(UserEntryPeer::ENTRY_ID);
		$this->filter->attachToCriteria($ueCrit);
		
		if(count ($entryIds))
		{
			$ueCrit->add(UserEntryPeer::ENTRY_ID, $entryIds, Criteria::IN);
		}
		
		$stmt = UserEntryPeer::doSelectStmt($ueCrit);
		$ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
		
		return $ids;
	}
	
	public function applyCondition(IKalturaDbQuery $query)
	{
		if ($this->disable)
		{
			return;
		}
		
		//get all userEntries
		$entryIds = $this->getEntryIds();
		
		/* @var $query KalturaCriteria */
		if (count($entryIds) <= $query->getLimit())
		{
			KalturaLog::info("Few user entries found - merge with query");
		}
		else 
		{
			KalturaLog::info("Too many user entries found");
			$this->disable = true;
			$entries = entryPeer::doSelect($query);
			
			//if few entry IDS - search userEntry table w/ entry IDs
			if (count ($entries) <= $query->getLimit())
			{
				KalturaLog::info("Few criteria entries found - cross with userEntries");
				
				$ids = array();
				foreach ($entries as $entry)
				{
					$ids[] = $entry->getId();
				}
				
				$entryIds = $this->getEntryIds($ids);
				
				if (count($entryIds) <= $query->getLimit())
				{
					KalturaLog::info("Few user entries found - merge with query");
				}
				else 
				{
					KalturaLog::info("Not all objects will return from the search - consider narrowing the search criteria");
					$entryIds = array_slice($entryIds, 0, $query->getLimit());
				}
			}
			else
			{
				KalturaLog::err("Consider narrowing search");
				throw new kCoreException(kCoreException::SEARCH_TOO_GENERAL);
			}
					
		}
		
		if (!count($entryIds))
		{
			KalturaLog::err("No user entries found - returning empty result");
			$entryIds = array (-1);
		}
		
		$query->addColumnWhere(entryPeer::ID, $entryIds, KalturaCriteria::IN);
		
	}
}
