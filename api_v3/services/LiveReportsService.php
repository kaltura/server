<?php

/**
 *
 * @service liveReports
 * @package api
 * @subpackage services
 */
class LiveReportsService extends KalturaBaseService
{
	
	/**
	 * @action getReport
	 * @param KalturaLiveReportType $reportType
	 * @param KalturaLiveReportInputFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaLiveStatsListResponse
	 */
	public function getReportAction($reportType, 
			KalturaLiveReportInputFilter $filter,
			KalturaFilterPager $pager)
	{
		$client = new WSLiveReportsClient();
		$wsFilter = $filter->getWSObject();
		
		switch($reportType) {
			case KalturaLiveReportType::ENTRY_GEO_TIME_LINE:
			case KalturaLiveReportType::ENTRY_SYNDICATION_TOTAL:
			case KalturaLiveReportType::ENTRY_TIME_LINE:
				return $this->requestClient($client, $reportType, $wsFilter);
				
			case KalturaLiveReportType::PARTNER_TOTAL:
				if($filter->live && empty($wsFilter->entryIds)) {
					$entryIds = $this->getAllLiveEntriesLiveNow();
					if(empty($entryIds))
						return new KalturaLiveStatsListResponse();
					
					$wsFilter->entryIds = $entryIds;
				}
				return $this->requestClient($client, $reportType, $wsFilter);
				
			case KalturaLiveReportType::ENTRY_TOTAL:
				if(!$filter->live) {
					$entryIds = $this->getLiveEntries($client, kCurrentContext::getCurrentPartnerId(), $pager);
					if(empty($entryIds))
						return new KalturaLiveStatsListResponse();
					
					$wsFilter->entryIds = $entryIds;
				}
				return $this->requestClient($client, $reportType, $wsFilter);
		}
		
	}
	
	/**
	 * Returns all live entry ids that are live now by partner id 
	 */
	protected function getAllLiveEntriesLiveNow() {
		// Partner ID condition is embeded in the default criteria.
		$baseCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$filter = new entryFilter();
		$filter->setTypeEquel(KalturaEntryType::LIVE_STREAM);
		$filter->setIsLive(true);
		$filter->attachToCriteria($baseCriteria);
		$baseCriteria->setSelectColumn(entryPeer::ID);
		
		$entryIds = entryPeer::doSelect($baseCriteria);
		
		return implode(",", $entryIds);
	}
	
	/**
	 * Returns all live entries that were live in the past X hours
	 */
	protected function getLiveEntries(WSLiveReportsClient $client, $partnerId, KalturaFilterPager $pager) {
		// Get live entries list
		/** @var WSLiveEntriesListResponse */
		$response = $client->getLiveEntries($partnerId);
		
		if($response->totalCount == 0)
			return null;
		
		// Hack to overcome the bug of single value
		$entryIds = $response->entries;
		if(!is_array($entryIds)) {
			$entryIds = array();
			$entryIds[] = $response->entries;
		}

		// Order entries by first broadcast
		$baseCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$filter = new entryFilter();
		$filter->setTypeEquel(KalturaEntryType::LIVE_STREAM);
		$filter->setIdIn($entryIds);
		$filter->attachToCriteria($baseCriteria);
		$baseCriteria->addDescendingOrderByColumn("entry.FIRST_BROADCAST");
		$pager->attachToCriteria($baseCriteria);
		$baseCriteria->setSelectColumn(entryPeer::ID);
		
		$entryIds = entryPeer::doSelect($baseCriteria);
		
		return implode(",", $entryIds);
	}
	
	protected function requestClient(WSLiveReportsClient $client, $reportType, $wsFilter) {
		/** @var WSLiveStatsListResponse */
		$result = $client->getReport($reportType, $wsFilter);
		$kResult = $result->toKalturaObject();
		return $kResult;
	}
}

