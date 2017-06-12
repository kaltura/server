;<?php
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
	const ENTRIES_COUNT = 200;
	const USER_ENTRIES_LIMIT = 2000;
	
	/**
	 * Function to retrieve
	 */
	public function getObjectCount()
	{
		$c = new Criteria();
		$filter->attachToCriteria($criteria);
		
		return UserEntryPeer::doCount($c);
	}
	
	public function getEntryIds($limit, $offset = 0, $entryIds = array())
	{
		$userEntryCriteria = new Criteria();
		
		$userEntryCriteria->addSelectColumn(UserEntryPeer::ENTRY_ID);
		if($this->filter)
			$this->filter->attachToCriteria($userEntryCriteria);
		
		if(count ($entryIds))
		{
			$userEntryCriteria->add(UserEntryPeer::ENTRY_ID, $entryIds, Criteria::IN);
		}
		
		$userEntryCriteria->add(UserEntryPeer::PARTNER_ID, kCurrentContext::$ks_partner_id);
		$userEntryCriteria->setLimit($limit);
		$userEntryCriteria->setOffset($offset);
		
		$stmt = UserEntryPeer::doSelectStmt($userEntryCriteria);
		$ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
		
		return $ids;
	}
	
	public function addToXml(SimpleXMLElement &$xmlElement)
	{
		parent::addToXml($xmlElement);
		
		if(!is_null($this->filter))
		{
			foreach($this->filter->getFields() as $name => $value)
			{
				if(!is_null($value))
					$xmlElement->addAttribute($name, $value);
			}
		}
	}
	
	public function fillObjectFromXml(SimpleXMLElement $xmlElement)
	{
		parent::fillObjectFromXml($xmlElement);
	
		$attr = $xmlElement->attributes();
		if(is_null($this->filter))
			$this->filter = new UserEntryFilter();
		foreach($attr as $name => $value)
		{
			if(!is_null($value))
			{
				$this->filter->set($name,(string)$value);
			}
		}
	}
	
	public function applyCondition(IKalturaDbQuery $query)
	{
		/* @var $query KalturaCriteria */
		if ($this->disable)
		{
			return;
		}
		$entryIds = array();
		$limit = $query->getLimit();
		
		$query->setLimit(self::ENTRIES_COUNT);
		$this->disable = true;
		
		$entries = entryPeer::doSelect($query);
		$totalCountEntries = $query->getRecordsCount();
		$query->setLimit($limit);
		
		if ($totalCountEntries <= self::ENTRIES_COUNT)
		{
			KalturaLog::info("Few entries found - merge with query");
			$ids = array();
			foreach ($entries as $entry)
			{
				$ids[] = $entry->getId();
			}
			
			$entryIds = $this->getEntryIds($limit, 0, $ids);
		}	
	
		else 
		{
			KalturaLog::info("Not all objects will return from the search - consider narrowing the search criteria");
			$userEntriesCount = 0;
			while (true)
			{
				if (count($entryIds) >= $limit)
				{
					KalturaLog::info("Enough entry IDs retrieved");
					break;
				}
				if ($userEntriesCount > self::USER_ENTRIES_LIMIT)
				{
					KalturaLog::info("User entry search limit reached!");
					break;
				}
				
				$currEntryIds = $this->getEntryIds($limit,$userEntriesCount);
				$query->addColumnWhere(entryPeer::ID, $currEntryIds, KalturaCriteria::IN);
				$query->forcedOrderIds = $currEntryIds;
				$entries = entryPeer::doSelect($query);
				foreach($entries as $entry)
				{
					if(count($entryIds) >= $limit)
					{
						break;
					}
					$entryIds[] = $entry->getId();
				}
				
				$userEntriesCount += $limit;
				kMemoryManager::clearMemory();
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
