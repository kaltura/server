<?php
require_once ( "defPartnerservices2Action.class.php");

class listbulkuploadsAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "listEntries",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array (
						),
					"optional" => array (
						)
					),
				"out" => array (
					"count" => array ("type" => "integer", "desc" => ""),
					"page_size" => array ("type" => "integer", "desc" => ""),
					"page" => array ("type" => "integer", "desc" => ""),
					"bulk_uploads" => array ("type" => "object" ),
					),
				"errors" => array (
				)
			);
	}

	protected function ticketType()
	{
		return self::REQUIED_TICKET_ADMIN;
	}

	protected function setExtraFilters ( entryFilter &$fields_set )	{	}
	
	protected function joinOnDetailed () { return true;}

	protected function getObjectPrefix () { return "entries"; } 

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$limit = $this->getP ( "page_size" , 20 );
		$limit = min ( $limit , 100 );

		$page = $this->getP ( "page" , 1 );

		$offset = ($page-1)* $limit;

		$c = new Criteria();
		$c->addAnd(BatchJobPeer::PARTNER_ID, $partner_id);
		$c->addAnd(BatchJobPeer::JOB_TYPE, BatchJobType::BULKUPLOAD);
		$c->addDescendingOrderByColumn(BatchJobPeer::ID);
		
		$count = BatchJobPeer::doCount($c);
		
		$c->setLimit($limit);
		$c->setOffset($offset);
		$jobs = BatchJobPeer::doSelect($c);
		
		$obj = array();
		foreach($jobs as $job) 
		{
			$jobData = $job->getData();
			
			if(!($jobData instanceof kBulkUploadJobData))
				continue;
				
			$bulkResults = BulkUploadResultPeer::retrieveWithEntryByBulkUploadId($job->getId());
			$obj[] = array (
				"uploadedBy" => $jobData->getUploadedBy(),
				"uploadedOn" => $job->getCreatedAt(null),
				"numOfEntries" => count($bulkResults),
				"status" => $job->getStatus(),
				"error" => ($job->getStatus() == BatchJob::BATCHJOB_STATUS_FAILED ? $job->getMessage() : ''),
				"logFileUrl" => requestUtils::getCdnHost() . "/index.php/extwidget/bulkuploadfile/id/{$job->getId()}/pid/{$job->getPartnerId()}/type/log",
				"csvFileUrl" => requestUtils::getCdnHost() . "/index.php/extwidget/bulkuploadfile/id/{$job->getId()}/pid/{$job->getPartnerId()}/type/csv"
			);
			
		}

		$this->addMsg ( "count" , $count );
		$this->addMsg ( "page_size" , $limit );
		$this->addMsg ( "page" , $page );

		$this->addMsg ( "bulk_uploads" , $obj );
	}
}
?>