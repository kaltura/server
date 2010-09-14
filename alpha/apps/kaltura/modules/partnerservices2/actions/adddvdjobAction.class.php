<?php
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

class adddvdjobAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "addDvdJob",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array (
						"entry_id" => array ("type" => "string", "desc" => ""),
						),
					"optional" => array (
						)
					),
				"out" => array (
					"batchjob" => array ("type" => "BatchJob", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_ENTRY_ID,
					)
			);
	}

	protected function ticketType()
	{
		return self::REQUIED_TICKET_REGULAR;
	}

	// ask to fetch the kuser from puser_kuser - so we can tel the difference between a
	public function needKuserFromPuser ( )
	{
		return self::KUSER_DATA_NO_KUSER;
	}

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$entry_id = $this->getPM ( "entry_id" );

		$entry = entryPeer::retrieveByPK( $entry_id );
		if ( ! $entry )
		{
			$this->addError ( APIErrors::INVALID_ENTRY_ID, "entry" , $entry_id );
		}
		else
		{
			$job = new BatchJob();
			$job->setJobType(BatchJob::BATCHJOB_TYPE_DVDCREATOR);
			$job->setStatus(BatchJob::BATCHJOB_STATUS_PENDING);
			$job->setCheckAgainTimeout(time() + 10);
			$job->setProgress(0);
			$job->setUpdatesCount(0);
			$job->setEntryId( $entry_id );
			$job->setPartnerId( $entry->getPartnerId());
			$job->setSubpId ( $entry->getSubpId());
		
			$job->save();
			
			$wrapper = objectWrapperBase::getWrapperClass( $job , objectWrapperBase::DETAIL_LEVEL_DETAILED );
			// TODO - remove this code when cache works properly when saving objects (in their save method)
			$wrapper->removeFromCache( "batch_job" , $job->getId() );
			$this->addMsg ( "batchjob" , $wrapper ) ;
		}
	}
}
?>