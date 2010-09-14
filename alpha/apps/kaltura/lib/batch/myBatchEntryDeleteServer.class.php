<?php
require_once( 'myBatchBase.class.php');

class myBatchEntryDeleteServer extends myBatchBase
{
	const MAX_DELETES_TO_HANDLE = 300;
	
	public function doDeleteLoop()
	{
		SET_CONTEXT ( "ENTRYDELETE");

		list ( $sleep_between_cycles ,
		$number_of_times_to_skip_writing_sleeping ) = self::getSleepParams( 'app_entry_delete_' );

		self::initDb();

		$temp_count = 0;
		while ( true )
		{
			self::exitIfDone();
			try
			{
				$this->doDeletes();
			}
			catch ( Exception $ex )
			{
				// TODO - log exceptions !!!
				// try to recover !!
				echo ( $ex );

				self::initDb( true );
				self::failed();
			}


			if ( $temp_count == 0 )
			{
				TRACE ( "Ended EntryDelete. sleeping for a while (" . $sleep_between_cycles .
				" seconds). Will write to the log in (" . ( $sleep_between_cycles * $number_of_times_to_skip_writing_sleeping ) . ") seconds" );
			}

			$temp_count++;
			if ($temp_count >= $number_of_times_to_skip_writing_sleeping ) $temp_count = 0;

			sleep ( $sleep_between_cycles );
		}
	}


	private function doDeletes ( $new_first = false )
	{
		if ( !BatchJob::isIndicatorSet( BatchJob::BATCHJOB_TYPE_DELETE ) ) return;
		
		TRACE ( "Indicator exists - removing it and checking DB" );
		BatchJob::removeIndicator( BatchJob::BATCHJOB_TYPE_DELETE );
		
		$c = new Criteria();
		$currentDc = kDataCenterMgr::getCurrentDc();
		$c->add(BatchJobPeer::DC, kDataCenterMgr::getCurrentDcId() );
		$c->add(BatchJobPeer::STATUS, BatchJob::BATCHJOB_STATUS_PENDING, Criteria::EQUAL);
		$c->add(BatchJobPeer::JOB_TYPE , BatchJob::BATCHJOB_TYPE_DELETE, Criteria::EQUAL); // handle only jobs of type import
		$c->setLimit( self::MAX_DELETES_TO_HANDLE );
		
		$jobs = BatchJobPeer::doSelect ($c);
			
		$c->clear();		
			
		foreach ( $jobs as $job )
		{
			// each job of this type has an entry_id
			// this entry is assumed to already be deleted but all the roughcuts pointing to it should be fixed
			$entry_id = $job->getEntryId();
			
			$roughcuts_for_entry = roughcutEntry::getAllRoughcuts( $entry_id );
			self::writeToLog ( "[" . count ( $roughcuts_for_entry ) . "] roughcuts for entry [$entry_id]" ); 
			foreach ( $roughcuts_for_entry as $roughcut )
			{
				//$roughcut = $roughcut_entry->getRoughcut();
				$roughcut_id = $roughcut->getId();
				self::writeToLog ( "[$entry_id] triggered a fixMetadata for roughcut [$roughcut_id]" ); 
				if ( $roughcut ) $roughcut->fixMetadata( true ); // increment the version of the metadata
			}
			
			$job->setStatus ( BatchJob::BATCHJOB_STATUS_FINISHED );
			$job->save();
		}
	}
 

	
	public function writeToLog( $message )
	{
		TRACE ( $message );
	}

}

?>