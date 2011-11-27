<?php
/**
 * @service reportAdmin
 * @package plugins.adminConsole
 * @subpackage api.services
 */
class ReportAdminService extends KalturaBaseService
{
	/**
	 * @action add
	 * @param KalturaReport $report
	 * @return KalturaReport
	 */
	function addAction(KalturaReport $report)
	{
		$dbReport = new Report();
		$report->toInsertableObject($dbReport);
		$dbReport->save();
		
		$report->fromObject($dbReport);
		return $report;
	}
	
	/**
	 * @action get
	 * @param int $id
	 * @return KalturaReport
	 */
	function getAction($id)
	{
		$dbReport = ReportPeer::retrieveByPK($id);
		if (is_null($dbReport))
			throw new KalturaAPIException(KalturaErrors::REPORT_NOT_FOUND, $id);
			
		$report = new KalturaReport();
		$report->fromObject($dbReport);
		return $report;
	}
	
	/**
	 * @action list
	 * @param KalturaReportFilter $filter
	 * @param KalturaReport $report
	 * @return KalturaReportListResponse
	 */
	function listAction(KalturaReportFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaReportFilter();
			
		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$reportFilter = new ReportFilter();
		
		$filter->toObject($reportFilter);
		$c = new Criteria();
		$reportFilter->attachToCriteria($c);
		$pager->attachToCriteria($c);
		
		$dbList = ReportPeer::doSelect($c);
		$c->setLimit(null);
		$totalCount = ReportPeer::doCount($c);

		$list = KalturaReportArray::fromDbArray($dbList);
		$response = new KalturaReportListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;    
	}
	
	/**
	 * @action update
	 * @param int $id
	 * @param KalturaReport $report
	 * @return KalturaReport
	 */
	function updateAction($id, KalturaReport $report)
	{
		$dbReport = ReportPeer::retrieveByPK($id);
		if (is_null($dbReport))
			throw new KalturaAPIException(KalturaErrors::REPORT_NOT_FOUND, $id);
			
		$report->toUpdatableObject($dbReport);
		$dbReport->save();
		
		$report->fromObject($dbReport);
		return $report;
	}
	
	/**
	 * @param int $id
	 * @action delete
	 */
	function deleteAction($id)
	{
		$dbReport = ReportPeer::retrieveByPK($id);
		if (is_null($dbReport))
			throw new KalturaAPIException(KalturaErrors::REPORT_NOT_FOUND, $id);
			
		$dbReport->setDeletedAt(time());
		$dbReport->save();
	}
	
	/**
	 * @action executeDebug
	 * @param int $id
	 * @param KalturaKeyValueArray $params
	 * @return KalturaReportResponse
	 */
	function executeDebugAction($id, KalturaKeyValueArray $params = null)
	{
		$dbReport = ReportPeer::retrieveByPK($id);
		if (is_null($dbReport))
			throw new KalturaAPIException(KalturaErrors::REPORT_NOT_FOUND, $id);
			
		$query = $dbReport->getQuery();
		$matches = null;
		$execParams = KalturaReportHelper::getValidateExecutionParameters($dbReport, $params);
		
		try 
		{
			$kReportsManager = new kReportManager($dbReport);
			list($columns, $rows) = $kReportsManager->execute($execParams);
		}
		catch(Exception $ex)
		{
			KalturaLog::err($ex);
			KalturaLog::info('Rethrowing KalturaAPIException with the exception details');
			throw new KalturaAPIException(KalturaErrors::INTERNAL_SERVERL_ERROR_DEBUG, $ex->getMessage());
		}
		
		$reportResponse = KalturaReportResponse::fromColumnsAndRows($columns, $rows);
		
		return $reportResponse;
	}
	
	/**
	 * @action getParameters
	 * @param int $id
	 * @return KalturaStringArray
	 */
	function getParametersAction($id)
	{
		$dbReport = ReportPeer::retrieveByPK($id);
		if (is_null($dbReport))
			throw new KalturaAPIException(KalturaErrors::REPORT_NOT_FOUND, $id);
			
		return KalturaStringArray::fromStringArray($dbReport->getParameters());
	}
	
	/**
	 * @action getCsvUrl
	 * @param int $id
	 * @param int $reportPartnerId
	 * @return string
	 */
	function getCsvUrlAction($id, $reportPartnerId)
	{
		$dbReport = ReportPeer::retrieveByPK($id);
		if (is_null($dbReport))
			throw new KalturaAPIException(KalturaErrors::REPORT_NOT_FOUND, $id);

		$dbPartner = PartnerPeer::retrieveByPK($reportPartnerId);
		if (is_null($dbPartner))
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $reportPartnerId);

		// allow creating urls for reports that are associated with partner 0 only
		if ($dbReport->getPartnerId() !== 0) 
			throw new KalturaAPIException(KalturaErrors::REPORT_NOT_FOUND, $id); 
		
		$ks = new ks();
		$ks->valid_until = time() + 2 * 365 * 24 * 60 * 60; // 2 years 
		$ks->type = ks::TYPE_KS;
		$ks->partner_id = $reportPartnerId;
		$ks->master_partner_id = null;
		$ks->partner_pattern = $reportPartnerId;
		$ks->error = 0;
		$ks->rand = microtime(true);
		$ks->user = '';
		$ks->privileges = 'setrole:REPORT_VIEWER_ROLE';
		$ks->additional_data = null;
		$ks_str = $ks->toSecureString();

		$url = "http://" . kConf::get("www_host") . "/api_v3/index.php/service/report/action/getCsv/id/{$id}/ks/" . $ks_str;
		return $url;
	}
}