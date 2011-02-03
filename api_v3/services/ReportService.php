<?php
/**
 * api for getting reports data by the report type and some inputFilter
 * @service report
 * @package api
 * @subpackage services
 */
class ReportService extends KalturaBaseService
{
	
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
	function getGraphsAction( $reportType , KalturaReportInputFilter $reportInputFilter , $dimension = null , $objectIds = null  )
	{
		$reportGraphs =  KalturaReportGraphArray::fromReportDataArray ( myReportsMgr::getGraph( $this->getPartnerId() , 
			$reportType , 
			$reportInputFilter->toReportsInputFilter() ,
			$dimension , 
			$objectIds ) );
//print_r ( $reportGraphs );
//		die();
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
	function getTotalAction( $reportType , KalturaReportInputFilter $reportInputFilter , $objectIds = null )
	{
		$reportTotal = new KalturaReportTotal();
		
		list ( $header , $data ) = myReportsMgr::getTotal( $this->getPartnerId() , 
			$reportType , 
			$reportInputFilter->toReportsInputFilter() , $objectIds );
		$reportTotal->fromReportTotal ( $header , $data );
			
		return $reportTotal;
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
	function getTableAction( $reportType , KalturaReportInputFilter $reportInputFilter , 
		KalturaFilterPager $pager , 
		$order = null , $objectIds = null )
	{
		$reportTable = new KalturaReportTable();
		
		list ( $header , $data , $totalCount ) = myReportsMgr::getTable( $this->getPartnerId() , 
			$reportType , 
			$reportInputFilter->toReportsInputFilter() ,
			$pager->pageSize , $pager->pageIndex ,
			$order ,  $objectIds);
		$reportTable->fromReportTable ( $header , $data , $totalCount );
			
		return $reportTable;
	}	
	
	/**
	 * 
	 * will create a Csv file for the given report and return the URL to access it
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
	function getUrlForReportAsCsvAction ( $reportTitle , $reportText , $headers , $reportType , KalturaReportInputFilter $reportInputFilter , 
		$dimension = null , 
		KalturaFilterPager $pager = null , 
		$order = null , $objectIds = null )
	{
		return myReportsMgr::getUrlForReportAsCsv( $this->getPartnerId() ,  $reportTitle , $reportText , $headers , $reportType , 
			$reportInputFilter->toReportsInputFilter() ,
			$dimension , 
			$objectIds ,
			$pager->pageSize , $pager->pageIndex , $order ); 
	}
}
?>