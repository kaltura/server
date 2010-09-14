<?php
require_once( 'myBatchBase.class.php');

/**
 * Will check all kind of stuff about the system
 * 1. validity of new entries
 * 2. number of files in directories
 * 3. 
 *
 * Will write to a log if not OK
 */
class myBatchHealthTest extends myBatchBase
{
	private static $batch_start_time;

	const SEVERITY_WARN = 1;	
	const SEVERITY_ERROR = 5;
	const SEVERITY_FATAL = 10;

	
	public function doHealthTestLoop()
	{
		self::$batch_start_time = time();
		SET_CONTEXT ( "HEALTH-TEST");

		list ( $sleep_between_cycles ,
		$number_of_times_to_skip_writing_sleeping ) = self::getSleepParams( 'app_health_test_' );

		self::initDb();

		$temp_count = 0;
		while ( true )
		{
			self::exitIfDone();
			try
			{
				$this->doHealthTest();
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
				TRACE ( "Ended HealthTest. sleeping for a while (" . $sleep_between_cycles .
				" seconds). Will write to the log in (" . ( $sleep_between_cycles * $number_of_times_to_skip_writing_sleeping ) . ") seconds" );
			}

			$temp_count++;
			if ($temp_count >= $number_of_times_to_skip_writing_sleeping ) $temp_count = 0;

			sleep ( $sleep_between_cycles );
		}
	}
	
	
	private function doHealthTest()
	{
		$this->validateEntries();
		
		$this->checkImportTable();
/*		
		$this->checkConversionTable();
		
		$this->checkIndicatorsDir();
*/
	}
	
	/**
	 * Will validate that the last entries all have good data 
	 *
	 */
	private function validateEntries ()
	{
		self::SRART ( __METHOD__ );
		
		static $last_check=null;
		if ( $last_check== null ) $last_check=self::$batch_start_time ; 
		
		$c = new Criteria();
		$c->addDescendingOrderByColumn( entryPeer::CREATED_AT );
//		$c->setLimit( 100 );
		$c->add ( entryPeer::CREATED_AT , $last_check , criteria::GREATER_EQUAL );
		$count = entryPeer::doCount( $c );
		
		$last_check = time();
		
		if ( $count > 0  )
		{
			if ( $count > 10 )
				$severity = self::SEVERITY_ERROR;
			else
				$severity = self::SEVERITY_WARN;
			self::ERROR ( __METHOD__ , $severity , "Entries with errors: " . print_r ( $entries , true ) ); 	
		}
		else
		{
			self::OK ( __METHOD__ );
		}
		
		self::END ( __METHOD__ );
	}
	
	
	/**
	 * will count 
	 *
	 */
	private function checkImportTable()
	{
		self::SRART ( __METHOD__ );
		
		static $last_check=null;
		if ( $last_check== null ) $last_check=self::$batch_start_time ; 
		
		$c = new Criteria();
		$c->addDescendingOrderByColumn( BatchJobPeer::CREATED_AT );
//		$c->setLimit( 101 );
		$c->add ( BatchJobPeer::JOB_TYPE , BATCHJOB_TYPE_IMPORT );
		$c->add ( BatchJobPeer::CREATED_AT , $last_check , criteria::GREATER_EQUAL );
		$c->add ( BatchJobPeer::STATUS , BatchJob::BATCHJOB_STATUS_PENDING );
		$count = BatchJobPeer::doCount( $c );
		
		$last_check = time();
		
		if ( $count > 40  )
		{
			if ( count ( $entries ) > 100 )
				$severity = self::SEVERITY_ERROR;
			else
				$severity = self::SEVERITY_WARN;
			self::ERROR ( __METHOD__ , $severity , "Entries with errors: " . print_r ( $entries , true ) ); 	
		}
		else
		{
			self::OK ( __METHOD__ );
		}
		
		self::END ( __METHOD__ );		
	}
	
	
	private static function ERROR ( $test , $severity , $str )
	{
		TRACE ( "!!! $test ($severity) - error: $str" );
	}
	
	private static function SRART ( $test  )
	{
		TRACE ( ">>> $test" );
	}

	private static function END ( $test  )
	{
		TRACE ( "<<< $test" );
	}
	
	private static function OK ( $test  )
	{
		TRACE ( "--- $test OK" );
	}
	
}

?>