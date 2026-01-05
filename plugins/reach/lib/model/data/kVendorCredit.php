
<?php

/**
 * Define vendor profile usage credit
 *
 * @package plugins.reach
 * @subpackage model
 *
 */
class kVendorCredit
{
	/**
	 * @var int
	 */
	protected $credit;

	/**
	 * @var string
	 */
	protected $fromDate;

	/**
	 * @var int
	 */
	protected $overageCredit;

	/**
	 * @var int
	 */
	protected $addOn;

	/**
	 * @var string
	 */
	public $lastSyncTime;

	/**
	 * @var int
	 */
	public $syncedCredit;

	/**
	 * @return the $credit
	 */
	public function getCredit()
	{
		return $this->credit;
	}

	/**
	 * @return the $fromDate
	 */
	public function getFromDate()
	{
		return $this->fromDate;
	}

	/**
	 * @return the $overageCredit
	 */
	public function getOverageCredit()
	{
		return $this->overageCredit;
	}


	/**
	 * @param int $credit
	 */
	public function setCredit($credit)
	{
		$this->credit = $credit;
	}

	/**
	 * @return the $addOn
	 */
	public function getAddOn()
	{
		return $this->addOn;
	}

	/**
	 * @param int $addOn
	 */
	public function setAddOn($addOn)
	{
		$this->addOn = $addOn;
	}

	/**
	 * @param string $fromDate
	 */
	public function setFromDate($fromDate)
	{
		$beginOfDay = kReachUtils::reachStrToTime("today", $fromDate);
		$this->fromDate = $beginOfDay;
	}

	/**
	 * @param int $overageCredit
	 */
	public function setOverageCredit($overageCredit)
	{
		$this->overageCredit = $overageCredit;
	}

	/**
	 * @return the $credit
	 */
	public function getSyncedCredit()
	{
		return $this->syncedCredit ? $this->syncedCredit : 0;
	}

	/**
	 * @param int $SyncedCredit
	 */
	public function setSyncedCredit($SyncedCredit)
	{
		$this->syncedCredit = $SyncedCredit;
	}


	/**
	 * @return string $lastSyncTime
	 */
	public function getLastSyncTime()
	{
		return $this->lastSyncTime;
	}

	/**
	 * @param string $lastSyncTime
	 */
	public function setLastSyncTime($lastSyncTime)
	{
		$this->lastSyncTime = $lastSyncTime;
	}

	public function addAdditionalCriteria(Criteria $c)
	{
	}

	public function isSynced()
	{
		return (intval(time() / 86400) == (intval($this->lastSyncTime / 86400)));
	}

	public function syncCreditPayPerUse($reachProfileId, $partnerId) {
		$c = new Criteria();
		$c->add(EntryVendorTaskPeer::REACH_PROFILE_ID, $reachProfileId);
		$c->add(EntryVendorTaskPeer::STATUS, EntryVendorTaskStatus::READY, Criteria::EQUAL);
		$c->add(EntryVendorTaskPeer::FINISH_TIME, $this->getSyncCreditStartDate(), Criteria::GREATER_EQUAL);
		$c->add(EntryVendorTaskPeer::PRICE, 0, Criteria::NOT_EQUAL);
		$c->add(EntryVendorTaskPeer::PARTNER_ID, $partnerId);
		$c->addGroupByColumn(EntryVendorTaskPeer::CATALOG_ITEM_ID);
		$c->addSelectColumn(EntryVendorTaskPeer::CATALOG_ITEM_ID);

		$stmt = EntryVendorTaskPeer::doSelectStmt($c);
		$allCatalogIds = $stmt->fetchAll(PDO::FETCH_COLUMN); // return distict catalog items

		// Step 2: Extract Pay Per Used catalog items (filter by payPerUse flag)
		$payPerUseCatalogIds = array();
		if (!empty($allCatalogIds)) {
			$catalogItems = VendorCatalogItemPeer::retrieveByPKs($allCatalogIds);
			foreach ($catalogItems as $catalogItem) {
				if ($catalogItem->getPayPerUse()) {
					$payPerUseCatalogIds[] = $catalogItem->getId();
				}
			}
		}

		// Step 3: Calculate only Pay Per Use credit for this sync period
		$totalPayPerUsePrice = 0;
		if (!empty($payPerUseCatalogIds)) {
			$c = new Criteria();
			$c->add(EntryVendorTaskPeer::REACH_PROFILE_ID, $reachProfileId);
			$c->add(EntryVendorTaskPeer::CATALOG_ITEM_ID, $payPerUseCatalogIds, Criteria::IN);
			$c->add(EntryVendorTaskPeer::STATUS, EntryVendorTaskStatus::READY, Criteria::EQUAL);
			$c->add(EntryVendorTaskPeer::FINISH_TIME, $this->getSyncCreditStartDate(), Criteria::GREATER_EQUAL);
			$c->add(EntryVendorTaskPeer::PRICE, 0, Criteria::NOT_EQUAL);
			$c->add(EntryVendorTaskPeer::PARTNER_ID, $partnerId);
			$c->addSelectColumn('SUM('. EntryVendorTaskPeer::PRICE .')');
			$this->addAdditionalCriteria($c);

			$stmt = EntryVendorTaskPeer::doSelectStmt($c);
			$row = $stmt->fetch(PDO::FETCH_NUM);
			$totalPayPerUsePrice = $row[0] ? $row[0] : 0;
		}

		return $totalPayPerUsePrice;
	}

	public function syncCreditNotPayPerUse($reachProfileId, $partnerId)
	{
		// Step 1: Get distinct catalog items based on queueTime
		$c = new Criteria();
		$c->add(EntryVendorTaskPeer::REACH_PROFILE_ID, $reachProfileId);
		$c->add(EntryVendorTaskPeer::STATUS, array(EntryVendorTaskStatus::PENDING, EntryVendorTaskStatus::PROCESSING, EntryVendorTaskStatus::READY), Criteria::IN);
		$c->add(EntryVendorTaskPeer::QUEUE_TIME, $this->getSyncCreditStartDate(), Criteria::GREATER_EQUAL);
		$c->add(EntryVendorTaskPeer::PRICE, 0, Criteria::NOT_EQUAL);
		$c->add(EntryVendorTaskPeer::PARTNER_ID, $partnerId);
		$c->addGroupByColumn(EntryVendorTaskPeer::CATALOG_ITEM_ID);
		$c->addSelectColumn(EntryVendorTaskPeer::CATALOG_ITEM_ID);

		$stmt = EntryVendorTaskPeer::doSelectStmt($c);
		$allCatalogIds = $stmt->fetchAll(PDO::FETCH_COLUMN); // return distict catalog items

		// Step 2: Extract non-PPU catalog items (filter by payPerUse flag)
		$nonPpuCatalogIds = array();
		if (!empty($allCatalogIds)) {
			$catalogItems = VendorCatalogItemPeer::retrieveByPKs($allCatalogIds);
			foreach ($catalogItems as $catalogItem) {
				if (!$catalogItem->getPayPerUse()) {
					$nonPpuCatalogIds[] = $catalogItem->getId();
				}
			}
		}

		// Step 3: Calculate only non-PPU credit for this sync period
		$totalNonPpuPrice = 0;
		if (!empty($nonPpuCatalogIds)) {
			$c = new Criteria();
			$c->add(EntryVendorTaskPeer::REACH_PROFILE_ID, $reachProfileId);
			$c->add(EntryVendorTaskPeer::CATALOG_ITEM_ID, $nonPpuCatalogIds, Criteria::IN);
			$c->add(EntryVendorTaskPeer::STATUS, array(EntryVendorTaskStatus::PENDING, EntryVendorTaskStatus::PROCESSING, EntryVendorTaskStatus::READY), Criteria::IN);
			$c->add(EntryVendorTaskPeer::QUEUE_TIME, $this->getSyncCreditStartDate(), Criteria::GREATER_EQUAL);
			$c->add(EntryVendorTaskPeer::PRICE, 0, Criteria::NOT_EQUAL);
			$c->add(EntryVendorTaskPeer::PARTNER_ID, $partnerId);
			$c->addSelectColumn('SUM('. EntryVendorTaskPeer::PRICE .')');
			$this->addAdditionalCriteria($c);

			$stmt = EntryVendorTaskPeer::doSelectStmt($c);
			$row = $stmt->fetch(PDO::FETCH_NUM);
			$totalNonPpuPrice = $row[0] ? $row[0] : 0;
		}

		// Return only the non-PPU portion calculated in this run
		return $totalNonPpuPrice;
	}

	public function syncCredit($reachProfileId, $partnerId)
	{
		$now = time();
		$totalUsedCredit = $this->getSyncedCredit();
		$totalPayPerUsePrice = $this->syncCreditPayPerUse($reachProfileId, $partnerId);
		$totalNonPayPerUsedCredit = $this->syncCreditNotPayPerUse($reachProfileId, $partnerId);
		$totalUsedCredit += ($totalNonPayPerUsedCredit + $totalPayPerUsePrice);
		$this->setSyncedCredit($totalUsedCredit);
		$this->setLastSyncTime($now);

		return $totalUsedCredit;
	}

	/***
	 * @param $includeOverages should return current credit including overageCredit info or not (Default is true)
	 * @return int
	 */
	public function getCurrentCredit($includeOverages = true, $validateActive = true)
	{
		$now = time();
		if ($validateActive && $now < $this->fromDate)
		{
				KalturaLog::debug("Current date [$now] is not in credit time Range [ from - $this->fromDate ] ");
				return 0;
		}
		
		$credit = $this->credit;
		if($includeOverages && $this->overageCredit)
		{
			$credit += $this->overageCredit;
		}

		if($this->addOn)
		{
			$credit += $this->addOn;
		}

		return $credit;
	}

	/***
	 * @param $time
	 * @return bool
	 */
	public function isActive($time = null)
	{
		$now = $time != null ? $time : time();
		if ( $now < $this->fromDate)
		{
			KalturaLog::debug("Current date [$now] is not in credit time Range [ from - $this->fromDate ] ");
			return false;
		}
		return true;
	}

	public function toDateHasExpired($now)
	{
		return false;
	}
	
	public function getSyncCreditStartDate()
	{
		return $this->getLastSyncTime() ? $this->getLastSyncTime() : $this->getFromDate();
	}

	public function shouldResetLastCreditExpiry($lastCreditExpiry)
	{
		return false;
	}

	/**
	 * @param $sourceCredit
	 */
	public function setInnerParams($sourceCredit)
	{
		if ($sourceCredit)
		{
			/** @var kVendorCredit $sourceCredit */
			$this->lastSyncTime = $sourceCredit->getLastSyncTime();
			$this->syncedCredit = $sourceCredit->getSyncedCredit();
		}
	}
}
