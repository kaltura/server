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
	const ENTRIES_COUNT = 100;
	
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
			$ueCrit->addAnd(UserEntryPeer::PRIVACY_CONTEXT, $privacyContexts, Criteria::IN);
		}
		
		$ueCrit->addSelectColumn(UserEntryPeer::ENTRY_ID);
		$this->filter->attachToCriteria($ueCrit);
		
		if(count ($entryIds))
		{
			$ueCrit->add(UserEntryPeer::ENTRY_ID, $entryIds, Criteria::IN);
		}
		$ueCrit->add(UserEntryPeer::PARTNER_ID, kCurrentContext::$ks_partner_id);
		$ueCrit->add(UserEntryPeer::KUSER_ID, kCurrentContext::$ks_kuser_id);
		
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
			$limit = $query->getLimit();
			$query->setLimit(self::ENTRIES_COUNT);
			$entries = entryPeer::doSelect($query);
			$query->setLimit($limit);
			
			$ids = array();
			foreach ($entries as $entry)
			{
				$ids[] = $entry->getId();
			}
			
			$entryIds = $this->getEntryIds($ids);
		
			if (count($entryIds) <= $limit)
			{
				KalturaLog::info("Few user entries found - merge with query");
			}
			else 
			{
				KalturaLog::info("Not all objects will return from the search - consider narrowing the search criteria");
				$entryIds = array_slice($entryIds, 0, $limit);
			}
		}
		
		if (!count($entryIds))
		{
			KalturaLog::err("No user entries found - returning empty result");
			$entryIds = array (-1);
		}
		
		if($query instanceof SphinxCriteria)
		{
			$query->forcedOrderIds = $entryIds;
		}
		
		$query->addColumnWhere(entryPeer::ID, $entryIds, KalturaCriteria::IN);
		
	}
}
