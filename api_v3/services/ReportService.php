<?php
/**
 * api for getting reports data by the report type and some inputFilter
 * @service report
 * @package api
 * @subpackage services
 */
class ReportService extends KalturaBaseService
{
	const MAX_CSV_FILE_NAME_LENGTH = 200;
	const MAX_EXPORT_GROUP_NAME_LENGTH = 100;
	protected static $crossPartnerReports = array(
		ReportType::PARTNER_USAGE,
		ReportType::VAR_USAGE,
		ReportType::VPAAS_USAGE_MULTI,
		ReportType::SELF_SERVE_USAGE_VPAAS,
	);

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		if (in_array(strtolower($actionName), array('execute', 'getcsv'), true))
		{
			$this->applyPartnerFilterForClass('Report');
		}
	}
	
	/* (non-PHPdoc)
	 * @see KalturaBaseService::partnerGroup()
	 */
	protected function partnerGroup($peer = null)
	{
		if (in_array(strtolower($this->actionName), array('execute', 'getcsv'), true))
			return $this->partnerGroup . ',0';
			
		return $this->partnerGroup;
	}
		
	/**
	 * Validates that all object ids are allowed partner ids
	 * 
	 * @param string $objectIds comma separated IDs
	 * @return string comma separated ids
	 */
	protected function validateObjectsAreAllowedPartners($reportType, $objectIds, $delimiter)
	{
		if(!$objectIds && $reportType == ReportType::SELF_SERVE_USAGE_VPAAS)
		{
			return null;
		}
		if(!$objectIds && $reportType != ReportType::VPAAS_USAGE_MULTI)
		{
			return $this->getPartnerId();
		}
			
		$c = new Criteria();
		$c->addSelectColumn(PartnerPeer::ID);
		$subCriterion1 = $c->getNewCriterion(PartnerPeer::PARTNER_PARENT_ID, $this->getPartnerId());
		$subCriterion2 = $c->getNewCriterion(PartnerPeer::ID, $this->getPartnerId());
		$subCriterion1->addOr($subCriterion2);
		$c->add($subCriterion1);
		if ($objectIds)
		{
			$c->add(PartnerPeer::ID, explode($delimiter, $objectIds), Criteria::IN);
		}
		
		$stmt = PartnerPeer::doSelectStmt($c);
		$partnerIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
		if (!$partnerIds)
			return Partner::PARTNER_THAT_DOWS_NOT_EXIST;

		return implode($delimiter, $partnerIds);
	}
		
	/**
	 * report getGraphs action allows to get a graph data for a specific report. 
	 * 
	 * @action getGraphs
	 * @param KalturaReportType $reportType  
	 * @param KalturaReportInputFilter $reportInputFilter
	 * @param string $dimension
	 * @param string $objectIds - one ID or more (separated by ',') of specific objects to query
	 * @return KalturaReportGraphArray 
	 */
	public function getGraphsAction( $reportType , KalturaReportInputFilter $reportInputFilter , $dimension = null , $objectIds = null, KalturaReportResponseOptions $responseOptions = null  )
	{
		if (!$responseOptions)
		{
			$responseOptions = new KalturaReportResponseOptions();
		}
		$kResponseOptions = $responseOptions->toObject();

		if(in_array($reportType, self::$crossPartnerReports))
			$objectIds = $this->validateObjectsAreAllowedPartners($reportType, $objectIds, $kResponseOptions->getDelimiter());
	
		$reportGraphs =  KalturaReportGraphArray::fromReportDataArray(kKavaReportsMgr::getGraph(
		    $this->getPartnerId(),
		    $reportType,
		    $reportInputFilter->toReportsInputFilter(),
		    $dimension,
		    $objectIds,
			$kResponseOptions),
			$kResponseOptions->getDelimiter());

		return $reportGraphs;
	}

	/**
	 * report getTotal action allows to get a graph data for a specific report. 
	 * 
	 * @action getTotal
	 * @param KalturaReportType $reportType  
	 * @param KalturaReportInputFilter $reportInputFilter
	 * @param string $objectIds - one ID or more (separated by ',') of specific objects to query
	 * @return KalturaReportTotal 
	 */
	public function getTotalAction( $reportType , KalturaReportInputFilter $reportInputFilter , $objectIds = null, KalturaReportResponseOptions $responseOptions = null)
	{
		if (!$responseOptions)
		{
			$responseOptions = new KalturaReportResponseOptions();
		}
		$kResponseOptions = $responseOptions->toObject();

		if(in_array($reportType, self::$crossPartnerReports))
			$objectIds = $this->validateObjectsAreAllowedPartners($reportType, $objectIds, $kResponseOptions->getDelimiter());

		$reportTotal = new KalturaReportTotal();
		
		list ( $header , $data ) = kKavaReportsMgr::getTotal(
		    $this->getPartnerId() ,
		    $reportType ,
		    $reportInputFilter->toReportsInputFilter() , $objectIds, $kResponseOptions);
		
		$reportTotal->fromReportTotal ( $header , $data, $kResponseOptions->getDelimiter() );
			
		return $reportTotal;
	}
	
	/**
	 * report getBaseTotal action allows to get the total base for storage reports  
	 * 
	 * @action getBaseTotal
	 * @param KalturaReportType $reportType  
	 * @param KalturaReportInputFilter $reportInputFilter
	 * @param string $objectIds - one ID or more (separated by ',') of specific objects to query
	 * @return KalturaReportBaseTotalArray 
	 */
	public function getBaseTotalAction( $reportType , KalturaReportInputFilter $reportInputFilter , $objectIds = null , KalturaReportResponseOptions $responseOptions = null)
	{
		if (!$responseOptions)
		{
			$responseOptions = new KalturaReportResponseOptions();
		}

		$reportSubTotals =  KalturaReportBaseTotalArray::fromReportDataArray(  
			kKavaReportsMgr::getBaseTotal( 
				$this->getPartnerId() , 
				$reportType , 
				$reportInputFilter->toReportsInputFilter() ,
				$objectIds,
				$responseOptions->toObject()));

		return $reportSubTotals;
	}
	
	/**
	 * report getTable action allows to get a graph data for a specific report. 
	 * 
	 * @action getTable
	 * @param KalturaReportType $reportType  
	 * @param KalturaReportInputFilter $reportInputFilter
	 * @param KalturaFilterPager $pager
	 * @param KalturaReportType $reportType 
	 * @param string $order
	 * @param string $objectIds - one ID or more (separated by ',') of specific objects to query
	 * @return KalturaReportTable 
	 */
	public function getTableAction($reportType, KalturaReportInputFilter $reportInputFilter, KalturaFilterPager $pager, $order = null, $objectIds = null, KalturaReportResponseOptions $responseOptions = null)
	{
		if (!$responseOptions)
		{
			$responseOptions = new KalturaReportResponseOptions();
		}
		$kResponseOptions = $responseOptions->toObject();

		$isCsv = false;
		if (kCurrentContext::$ks_partner_id == Partner::BATCH_PARTNER_ID)
		{
			$isCsv = true;
		}

		if(in_array($reportType, self::$crossPartnerReports))
			$objectIds = $this->validateObjectsAreAllowedPartners($reportType, $objectIds, $kResponseOptions->getDelimiter());

		$reportTable = new KalturaReportTable();

		// Temporary hack to allow admin console to request a report for any partner
		//	can remove once moving to Kava
		$partnerId = $this->getPartnerId();
		if ($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID && $objectIds && ctype_digit($objectIds))
		{
			$partnerReports = array(
				KalturaReportType::VAR_USAGE,
				KalturaReportType::VPAAS_USAGE,
				KalturaReportType::ENTRY_USAGE,
				KalturaReportType::PARTNER_USAGE,
			);

			if (in_array($reportType, $partnerReports))
			{
				$partnerId = $objectIds;
			}
		}
		
		list ( $header , $data , $totalCount ) = kKavaReportsMgr::getTable(
		    $partnerId ,
		    $reportType ,
		    $reportInputFilter->toReportsInputFilter() ,
		    $pager->pageSize , $pager->pageIndex ,
		    $order , $objectIds, null , $isCsv , $kResponseOptions);

		$reportTable->fromReportTable ( $header , $data , $totalCount, $kResponseOptions->getDelimiter() );
			
		return $reportTable;
	}	
	
	/**
	 * 
	 * will create a CSV file for the given report and return the URL to access it
	 * @action getUrlForReportAsCsv
	 * 
	 * @param string $reportTitle The title of the report to display at top of CSV 
	 * @param string $reportText The text of the filter of the report
	 * @param string $headers The headers of the columns - a map between the enumerations on the server side and the their display text  
	 * @param KalturaReportType $reportType  
	 * @param KalturaReportInputFilter $reportInputFilter
	 * @param string $dimension	  
	 * @param KalturaFilterPager $pager
	 * @param KalturaReportType $reportType 
	 * @param string $order
	 * @param string $objectIds - one ID or more (separated by ',') of specific objects to query
	 * @return string 
	 */
	public function getUrlForReportAsCsvAction ( $reportTitle , $reportText , $headers , $reportType , KalturaReportInputFilter $reportInputFilter , 
		$dimension = null , 
		KalturaFilterPager $pager = null , 
		$order = null , $objectIds = null,
		KalturaReportResponseOptions $responseOptions = null)
	{
		ini_set( "memory_limit","512M" );
		
		if(!$pager)
			$pager = new KalturaFilterPager();

		if (!$responseOptions)
		{
			$responseOptions = new KalturaReportResponseOptions();
		}
		$kResponseOptions = $responseOptions->toObject();

		if(in_array($reportType, self::$crossPartnerReports))
			$objectIds = $this->validateObjectsAreAllowedPartners($reportType, $objectIds, $kResponseOptions->getDelimiter());

		try {
			$report = kKavaReportsMgr::getUrlForReportAsCsv(
				$this->getPartnerId(),
				$reportTitle,
				$reportText,
				$headers,
				$reportType,
				$reportInputFilter->toReportsInputFilter(),
				$dimension,
				$objectIds,
				$pager->pageSize,
				$pager->pageIndex,
				$order,
				$kResponseOptions);
		}
		catch(Exception $e){
			$code = $e->getCode();
			if ($code == kCoreException::SEARCH_TOO_GENERAL)
					throw new KalturaAPIException(KalturaErrors::SEARCH_TOO_GENERAL);
		}

		if ((infraRequestUtils::getProtocol() == infraRequestUtils::PROTOCOL_HTTPS))
			$report = str_replace("http://","https://",$report);

		return $report;
	}
	
	/**
	 *
	 * Will serve a requested report
	 * @action serve
	 * 
	 * @param string $id - the requested id
	 * @return string
	 * @ksOptional 
	 */
	public function serveAction($id) {
		// KS verification - we accept either admin session or download privilege of the file
		$ks = $this->getKs();
		if(!$ks || !($ks->isAdmin() || $ks->verifyPrivileges(ks::PRIVILEGE_DOWNLOAD, $id)))
			KExternalErrors::dieError(KExternalErrors::ACCESS_CONTROL_RESTRICTED);

		if(!preg_match('/^[\w-_]*$/', $id))
			throw new KalturaAPIException(KalturaErrors::REPORT_NOT_FOUND, $id);

		$partner_id = $this->getPartnerId();
		$folderPath = "/content/reports/$partner_id";
		$fullPath = myContentStorage::getFSContentRootPath() . $folderPath;
		$file_path = "$fullPath/$id";

		return $this->dumpFile($file_path, 'text/csv');
	}
	
	/**
	 * @action execute
	 * @param bigint $id
	 * @param KalturaKeyValueArray $params
	 * @return KalturaReportResponse
	 */
	public function executeAction($id, KalturaKeyValueArray $params = null)
	{
		$dbReport = ReportPeer::retrieveByPK($id);
		if (is_null($dbReport))
			throw new KalturaAPIException(KalturaErrors::REPORT_NOT_FOUND, $id);
			
		$this->addPartnerIdToParams($params);
		
		$execParams = KalturaReportHelper::getValidateExecutionParameters($dbReport, $params);
		
		$kReportsManager = new kReportManager($dbReport);
		list($columns, $rows) = $kReportsManager->execute($execParams);
		
		$reportResponse = KalturaReportResponse::fromColumnsAndRows($columns, $rows);
		
		return $reportResponse;
	}

	/**
	 * @action getCsv
	 * @param bigint $id
	 * @param KalturaKeyValueArray $params
	 * @return file
	 * @param string $excludedFields
	 * @throws KalturaAPIException
	 */
	public function getCsvAction($id, KalturaKeyValueArray $params = null, $excludedFields = null)
	{
		$this->addPartnerIdToParams($params);

		ini_set( "memory_limit","1024M" );
		set_time_limit(600);

		if (kKavaBase::isPartnerAllowed($this->getPartnerId(), kKavaBase::VOD_DISABLED_PARTNERS))
		{
			$customReports = kConf::getMap('custom_reports');
			if (!isset($customReports[$id]))
				throw new KalturaAPIException(KalturaErrors::REPORT_NOT_FOUND, $id);

			list($columns, $rows) = kKavaReportsMgr::customReport($id, $params->toObjectsArray());
		}
		else
		{
			$dbReport = ReportPeer::retrieveByPK($id);
			if (is_null($dbReport))
				throw new KalturaAPIException(KalturaErrors::REPORT_NOT_FOUND, $id);

			$execParams = KalturaReportHelper::getValidateExecutionParameters($dbReport, $params);

			$kReportsManager = new kReportManager($dbReport);
			list($columns, $rows) = $kReportsManager->execute($execParams);
		}

		$fileName = 'Report_' . $id . '_' . $this->getPartnerId();
		foreach($params as $param)
		{
			$tempName = '_' . $param->key . '_' . $param->value;
			if (strlen($fileName) + strlen($tempName) >= self::MAX_CSV_FILE_NAME_LENGTH)
			{
				break;
			}
			$fileName .= $tempName;
		}

		$fileName = $fileName . '.csv';

		header('Content-Type: text/csv');
		header("Content-Disposition: attachment; filename=\"$fileName\"");
		$content = "\xEF\xBB\xBF"; // a fix for excel, copied from myReportsMgr

		if($excludedFields)
		{
			KCsvWrapper::hideCsvColumns($excludedFields, $columns, $rows);
		}

		$content .= implode(',', $columns) . "\n";
		foreach($rows as $row)
		{
			$row = str_replace(',', ' ', $row);
			$content .= implode(',', $row) . "\n";
		}
		return new kRendererString($content, 'text/csv');
	}

	/**
	 * Returns report CSV file executed by string params with the following convention: param1=value1;param2=value2
	 * excludedFields can be supplied comma separated
	 *
	 * @action getCsvFromStringParams
	 * @param bigint $id
	 * @param string $params
	 * @param string $excludedFields
	 * @return file
	 * @throws KalturaAPIException
	 */
	public function getCsvFromStringParamsAction($id, $params = null, $excludedFields = null)
	{
		$paramsArray = $this->parseParamsStr($params);
		return $this->getCsvAction($id, $paramsArray, $excludedFields);
	}

	/**
	 * @action exportToCsv
	 * @param KalturaReportExportParams $params
	 * @return KalturaReportExportResponse
	 * @throws KalturaAPIException
	 */
	public function exportToCsvAction(KalturaReportExportParams $params)
	{
		$this->validateReportExportParams($params);

		if (!$params->recipientEmail)
		{
			$kuser = kCurrentContext::getCurrentKsKuser();
			if ($kuser)
			{
				$params->recipientEmail = $kuser->getEmail();
			}
			else
			{
				$partnerId = kCurrentContext::getCurrentPartnerId();
				$partner = PartnerPeer::retrieveByPK($partnerId);
				$params->recipientEmail = $partner->getAdminEmail();
			}
		}

		if (strlen($params->reportsItemsGroup) > self::MAX_EXPORT_GROUP_NAME_LENGTH)
		{
			$params->reportsItemsGroup = substr($params->reportsItemsGroup,0, self::MAX_EXPORT_GROUP_NAME_LENGTH);
		}

		$dbBatchJob = kJobsManager::addExportReportJob($params);

		$response = new KalturaReportExportResponse();
		$response->referenceJobId = $dbBatchJob->getId();
		$response->reportEmail = $params->recipientEmail;

		return $response;
	}

	protected function validateReportExportParams(KalturaReportExportParams $params)
	{
		if (!$params->reportItems)
		{
			throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER);
		}
		foreach ($params->reportItems as $reportItem)
		{
			/**
			 * @var KalturaReportExportItem $reportItem
			 */
			if (!$reportItem->action || !$reportItem->reportType || !$reportItem->filter)
			{
				throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER);
			}
		}

		if ($params->reportsItemsGroup && !preg_match('/^\w[\w\s]*$/', $params->reportsItemsGroup))
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_REPORT_ITEMS_GROUP);
		}

		$customUrlPartnerIds = kConf::get(kFlowHelper::EXPORT_REPORT_CUSTOM_URL, kConfMapNames::ANALYTICS, array());
		if (in_array($this->getPartnerId(), $customUrlPartnerIds) && !$params->baseUrl)
		{
			throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, 'baseUrl');
		}

	}

	protected function parseParamsStr($paramsStr)
	{
		$paramsStrArray = explode(';', $paramsStr);
		$paramsKeyValueArray = new KalturaKeyValueArray();
		foreach($paramsStrArray as $paramStr)
		{
			$paramStr = trim($paramStr);
			$paramArray = explode('=', $paramStr, 2);
			$paramKeyValue = new KalturaKeyValue();
			$paramKeyValue->key = isset($paramArray[0]) ? $paramArray[0] : null;
			$paramKeyValue->value = isset($paramArray[1]) ? $paramArray[1] : null;
			$paramsKeyValueArray[] = $paramKeyValue;
		}
		return $paramsKeyValueArray;
	}
	
	protected function addPartnerIdToParams($params)
	{
		// remove partner id parameter
		foreach($params as $param)
		{
			if (strtolower($param->key) == 'partner_id')
			{
				$param->key = '';
				$param->value = '';
			}
		}
		// force partner id parameter
		$partnerIdParam = new KalturaKeyValue();
		$partnerIdParam->key = 'partner_id';
		$partnerIdParam->value = $this->getPartnerId();
		$params[] = $partnerIdParam;
	}
}
