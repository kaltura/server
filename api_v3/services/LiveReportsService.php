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
	 * @action getEvents
	 * @param KalturaLiveReportType $reportType
	 * @param KalturaLiveReportInputFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaReportGraphArray
	 */
	public function getEventsAction($reportType,
			KalturaLiveReportInputFilter $filter = null,
			KalturaFilterPager $pager = null)
	{
		if(is_null($filter))
			$filter = new KalturaLiveReportInputFilter();
		if(is_null($pager))
			$pager = new KalturaFilterPager;
		
		$client = new WSLiveReportsClient();
		$wsFilter = $filter->getWSObject();
		$wsFilter->partnerId = kCurrentContext::getCurrentPartnerId();
		$wsPager = new WSLiveReportInputPager($pager->pageSize, $pager->pageIndex);
		
		$wsResult = $client->getEvents($reportType, $wsFilter, $wsPager);
		$resultsArray = array();
		$objects = explode(";", $wsResult->objects);
		foreach($objects as $object) {
			if(empty($object))
				continue;
			
			$parts = explode(",", $object);
			$resultsArray[$parts[0]] = $parts[1];
		}
		
		$kResult = KalturaReportGraphArray::fromReportDataArray(array("audience" => $resultsArray));
		
		return $kResult;
	}
	
	/**
	 * @action getReport
	 * @param KalturaLiveReportType $reportType
	 * @param KalturaLiveReportInputFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaLiveStatsListResponse
	 */
	public function getReportAction($reportType, 
			KalturaLiveReportInputFilter $filter = null,
			KalturaFilterPager $pager = null)
	{
		if(is_null($filter))
			$filter = new KalturaLiveReportInputFilter();
		if(is_null($pager))
			$pager = new KalturaFilterPager();
		
		$client = new WSLiveReportsClient();
		$wsFilter = $filter->getWSObject();
		$wsFilter->partnerId = kCurrentContext::getCurrentPartnerId();
		
		$wsPager = new WSLiveReportInputPager($pager->pageSize, $pager->pageIndex);
		
		switch($reportType) {
			case KalturaLiveReportType::ENTRY_GEO_TIME_LINE:
			case KalturaLiveReportType::ENTRY_SYNDICATION_TOTAL:
				return $this->requestClient($client, $reportType, $wsFilter, $wsPager);
				
			case KalturaLiveReportType::PARTNER_TOTAL:
				if($filter->live && empty($wsFilter->entryIds)) {
					$entryIds = $this->getAllLiveEntriesLiveNow();
					if(empty($entryIds)) {
						$response = new KalturaLiveStatsListResponse();
						$response->totalCount = 1;
						$response->objects = array();
						$response->objects[] = new KalturaLiveStats();
						return $response;
					}
					
					$wsFilter->entryIds = $entryIds;
				}
				return $this->requestClient($client, $reportType, $wsFilter, $wsPager);
				
			case KalturaLiveReportType::ENTRY_TOTAL:
				$totalCount = null;
				if(!$filter->live && empty($wsFilter->entryIds)) {
					list($entryIds, $totalCount) = $this->getLiveEntries($client, kCurrentContext::getCurrentPartnerId(), $pager);
					if(empty($entryIds))
						return new KalturaLiveStatsListResponse();
					
					$wsFilter->entryIds = implode(",", $entryIds);
				}
				
				/** @var KalturaLiveStatsListResponse */
				$result = $this->requestClient($client, $reportType, $wsFilter, $wsPager);
				if($totalCount)
					$result->totalCount = $totalCount;
				
				return $result;
		}
		
	}
	
	/**
	 * @action exportToCsv
	 * @param KalturaLiveReportExportType $reportType 
	 * @param string $entryIds
	 * @param string $recpientEmail
	 * @return KalturaLiveStatsListResponse
	 */
	public function exportToCsvAction($reportType, $entryIds = null, $recpientEmail = null)
	{
		// @_!! DO SOMETHING
		$outputPath = "/opt/kaltura/tmp/convert";
		kJobsManager::addExportLiveReportJob($reportType, $entryIds, $outputPath, $recpientEmail);
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
		$filter->setPartnerSearchScope(baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE);
		$filter->attachToCriteria($baseCriteria);
		
		$entries = entryPeer::doSelect($baseCriteria);
		$entryIds = array();
		foreach($entries as $entry)
			$entryIds[] = $entry->getId();
		
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
		$filter->setPartnerSearchScope(baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE);
		$filter->attachToCriteria($baseCriteria);
		$baseCriteria->addDescendingOrderByColumn("entry.FIRST_BROADCAST");
		$pager->attachToCriteria($baseCriteria);
		
		$entries = entryPeer::doSelect($baseCriteria);
		$entryIds = array();
		foreach($entries as $entry)
			$entryIds[] = $entry->getId();
		
		$totalCount = $baseCriteria->getRecordsCount();
		return array($entryIds, $totalCount);
	}
	
	protected function requestClient(WSLiveReportsClient $client, $reportType, $wsFilter, $wsPager) {
		/** @var WSLiveStatsListResponse */
		$result = $client->getReport($reportType, $wsFilter, $wsPager);
		$kResult = $result->toKalturaObject();
		return $kResult;
	}
}

