<?php
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

class queuependingbatchjobAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "queuePendingBatchJob",
				"desc" => "move one pending batch job to queued state according to given criteria" ,
				"in" => array (
					"mandatory" => array (
						"job_type" => array ("type" => "enum,BatchJob,BATCHJOB_TYPE", "desc" => ""), 
						"processor_name" => array ("type" => "string", "desc" => ""),
						"processor_timeout" => array ("type" => "string", "desc" => ""),
						),
					"optional" => array (
						"over_quota_partners" => array ("type" => "string", "desc" => ""),
						"defered_partners" => array ("type" => "string", "desc" => ""),
					)
					),
				"out" => array (
					"batchjob" => array ("type" => "BatchJob", "desc" => "")
					),
				"errors" => array (
					//APIErrors::INVALID_ENTRY_ID,
					//APIErrors::INVALID_ENTRY_VERSION,
					)
			);
	}

	protected function ticketType()
	{
		return self::REQUIED_TICKET_ADMIN;
	}

	public function needKuserFromPuser ( )
	{
		return self::KUSER_DATA_NO_KUSER;
	}

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$job_type = $this->getPM ( "job_type" );
    	$processor_name = $this->getPM ( "processor_name" );
	    $processor_timeout = $this->getPM ( "processor_timeout" );
	    $over_quota_partners = $this->getPM ( "over_quota_partners" );
	    $defered_partners = $this->getPM ( "defered_partners" );

		$con = Propel::getConnection();
		
		// fetch one pending row of a given job_type 
		// where the over_quota_partner requests are dropped
		// and defered_partners request are of less priority
		
		$query = "SELECT ".BatchJobPeer::ID." FROM ".BatchJobPeer::TABLE_NAME. " WHERE ".
			BatchJobPeer::STATUS."=".BatchJob::BATCHJOB_STATUS_PENDING." AND ".
			BatchJobPeer::JOB_TYPE."=".$job_type." ".
			(count($over_quota_partners) ? (" AND ".BatchJobPeer::PARTNER_ID." NOT IN ($over_quota_partners) ") : "").
			" ORDER BY ".
			(count($defered_partners) ? (BatchJobPeer::PARTNER_ID." IN ($defered_partners), ") : "").
			BatchJobPeer::CREATED_AT. " LIMIT 1";
				
		$statement = $con->prepareStatement($query);
	    $resultset = $statement->executeQuery();
	    
	    $batch_job = null;
	    while ($resultset->next())
	    {
			$batch_job = BatchJobPeer::retrieveByPK($resultset->getInt('ID'));
	    }
	    
    
		$batch_job = BatchJobPeer::doSelectOne($c);
		
		if ($batch_job)
		{
			// force update to work on the currently selected row and ensure it is still in pending status
			$c->add(BatchJobPeer::ID, $batch_job->getId()); 
			$c->add(BatchJobPeer::STATUS, BatchJob::BATCHJOB_STATUS_PENDING);
			
			$update = new Criteria();
			$update->add(BatchJobPeer::STATUS, BatchJob::BATCHJOB_STATUS_QUEUED);
			$update->add(BatchJobPeer::PROCESSOR_NAME, $processor_name);
			$update->add(BatchJobPeer::PROCESSOR_EXPIRATION, time() + $processor_timeout);
			
			$affectedRows = BasePeer::doUpdate($c, $update, $con);
			if ( $affectedRows != 1 )
				$batch_job = null;
		}
		
		if ( ! $batch_job )
		{
			//xx$this->addError ( APIErrors::INVALID_ENTRY_ID, $this->getObjectPrefix() , $entry_id );
		}
		else
		{
			$wrapper = objectWrapperBase::getWrapperClass( $batch_job , objectWrapperBase::DETAIL_LEVEL_REGULAR );
			// TODO - remove this code when cache works properly when saving objects (in their save method)
			$wrapper->removeFromCache( "batch_job" , $batch_job->getId() );
			$this->addMsg ( "batchjob" , $wrapper ) ;
		}
	}
}
?>