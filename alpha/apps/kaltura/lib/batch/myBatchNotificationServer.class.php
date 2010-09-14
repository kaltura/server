<?php
require_once( 'myBatchBase.class.php');

class myBatchNotificationServer extends myBatchBase
{
	const MAX_NOTIFICATIONS_TO_HANDLE = 300;
	
	const KALTURA_NOTIFICAITON_ERROR_EMAIL = 70;
	 
	private static $curl;
	
	private static $partner_ids; 
	
	// TODO - implement call to notification table
	public static function getBatchStatus( $args )	
	{	
		$batch_status = new batchStatus();
		$batch_status->batch_name = $args[0];
		$stats = self::getJobStats( 2400 );
		$batch_status->addToPending( "DB:notification, status=" . BatchJob::BATCHJOB_STATUS_PENDING ,
			@$stats[BatchJob::BATCHJOB_STATUS_PENDING]["count"]);
		$batch_status->addToInProc( "" ,  0 );//@$stats["full_stats"][BatchJob::BATCHJOB_STATUS_PROCESSING]["count"];
		
		$batch_status->succeedded_in_period = @$stats["full_stats"][ BatchJob::BATCHJOB_STATUS_FINISHED]["count"];
		$batch_status->failed_in_period = @$stats["full_stats"] [BatchJob::BATCHJOB_STATUS_FAILED]["count"];
		
		$batch_status->last_log_time  = @$stats["log_timestamp"];
		return $batch_status; 
	}

	private static function getJobStats ( $hours_ago = 24 )
	{
		$connection = Propel::getConnection();
		$from_date = date("Y-m-d H:i:s", time()- $hours_ago * 3600 ); 
		// don't fetch status 7=aborted
		// need type 1=import, 3=flatten, 4=bulkupload and 6=download
		// select status = 5 as well , but don't include in problematic count
	    $query = "select status , UNIX_TIMESTAMP(min(created_at)) as oldest , UNIX_TIMESTAMP(max(created_at)) as newest , count(1) as cnt " .
	    	"from notification  where status in (1,2,3) and created_at>\"$from_date\" " . 
	    	"group by status";
		
		
		$statement = $connection->prepareStatement($query);
		$resultset = $statement->executeQuery();	

		$notification_stats = array();
		while ($resultset->next())
	    {
	    	$status = $resultset->getInt('status');
			$oldest = $resultset->getInt('oldest');
	    	$newest = $resultset->getInt('newest');
	    	$count = $resultset->getInt('cnt');

//	    	if ( $status == BatchJob::BATCHJOB_STATUS_PENDING)
	    	{
		    	// foreach job_type - creaet an array per status
		    	$notification_stats [$status] = array ( "oldest" => $oldest , "newest" => $newest , "count" => $count ) ;
		    }
	    }

		return $notification_stats;
				
	}
		
	public function myBatchNotificationServer ( $script_name , $partner_ids = false  )
	{
		$this->script_name = $script_name;
		// testnotificationAction invokes 'sendNotification' but does not create real batch job
		if ($script_name) 
		{
			$this->register( $script_name );
		}
		
		self::$partner_ids = explode ( "," , $partner_ids );
		SET_CONTEXT ( "NTFC [$partner_ids]");
	}
	
	public function doNotifyLoop()
	{
		$this->m_config = self::getConfig( 'app_notifications_' );
		
		list ( $sleep_between_cycles ,
		$number_of_times_to_skip_writing_sleeping ) = self::getSleepParams( 'app_notifications_' );

		self::initDb();

		$temp_count = 0;
		while ( true )
		{
			self::exitIfDone();
			try
			{
				$this->doNotifications();
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
				TRACE ( "Ended notifications. sleeping for a while (" . $sleep_between_cycles .
				" seconds). Will write to the log in (" . ( $sleep_between_cycles * $number_of_times_to_skip_writing_sleeping ) . ") seconds" );
			}

			$temp_count++;
			if ($temp_count >= $number_of_times_to_skip_writing_sleeping ) $temp_count = 0;

			sleep ( $sleep_between_cycles );
		}
	}


	private function doNotifications ( $new_first = false )
	{
		static $ignore_indicator_count = 0;

		$ignore_indicator_max_count = $this->m_config->get ( "ignore_indicator_max_count" );
		if ( $ignore_indicator_count > $ignore_indicator_max_count )
		{
			// from time to time - execte function even if no indicator - this is to handle notifications that failed
			$ignore_indicator_count = 0;
			TRACE ( "Indicator DOES NOT exist - Checking periodically for failed notifications (once every [$ignore_indicator_max_count] rounds)." );
			
		}
		else
		{
			$ignore_indicator_count++;	
			if ( !kNotificationJobData::isIndicatorSet() ) return;
		
			TRACE ( "Indicator exists - removing it and checking DB" );
			kNotificationJobData::removeIndicator();
			
		}
		
		$c = new Criteria();
//		$c->add ( notificationPeer::PARTNER_ID , self::$partner_ids , Criteria::In );
		
		// get either notifications with status PENDING or ones that should be re-sent and are XXX seconds old
		$secs_betwee_retry = $this->m_config->get ( "seconds_between_retry_notify" );
		
		$relevant_time = time() - $secs_betwee_retry;
		$query_relevant_time =  date('Y-m-d H:i:s', $relevant_time);
		$resent_crit = $c->getNewCriterion( notificationPeer::STATUS ,  BatchJob::BATCHJOB_STATUS_RETRY);
		$resent_crit->addAnd ( $c->getNewCriterion( notificationPeer::UPDATED_AT ,  $query_relevant_time , Criteria::LESS_EQUAL ) );
		$crit = $c->getNewCriterion( notificationPeer::STATUS ,  BatchJob::BATCHJOB_STATUS_PENDING );
		$crit->addOr ( $resent_crit );
		//
/* 
		$c->add ( notificationPeer::STATUS , 
			array ( BatchJob::BATCHJOB_STATUS_PENDING , BatchJob::BATCHJOB_STATUS_RETRY ) , Criteria::IN );
*/
		$c->add ( $crit );
		$c->addAscendingOrderByColumn ( notificationPeer::OBJECT_ID ); // collect all notifications for a specific object in one bulk
		if ( $new_first )
			$c->addDescendingOrderByColumn( notificationPeer::CREATED_AT );
		else
			$c->addAscendingOrderByColumn ( notificationPeer::CREATED_AT );
			
		$c->setLimit( self::MAX_NOTIFICATIONS_TO_HANDLE );
		 
		$c->add(notificationPeer::DC, kDataCenterMgr::getCurrentDcId() );

		$notifications = notificationPeer::doSelect( $c );

		// TODO - eventually all partners will support multiNotifications 
		// when so - can remove some of the code
		// see  which notifications should go to multiNotification and which should stay in single notificaiton
		list ( $single_notifications , $multi_notifications ) = $this->splitToMulti ( $notifications );
//TRACE ( "single: " . print_r ( $single_notifications , true ) );
//TRACE ( "multi: " . print_r ( $multi_notifications , true ) ) ;
		foreach ( $multi_notifications as $partner_id => $multi_notifications_per_partner )
		{
			$partner = partnerPeer::retrieveByPK( $partner_id );
			if ( !$partner )
			{
				TRACE ( "Error. notification [{$not->getId()}] of type [{$not->getType()}] has a partner_id [$partner_id] but no such partner in the system.");
				continue;
			}

			TRACE ( "Sending multi-notifications to partner [$partner_id]" );
			// we assume that the partner wants notificatins or else it would have not appeared in the DB			
			list ( $url , $signature_key ) = myNotificationMgr::getPartnerNotificationInfo ( $partner );
			list ( $params_sent , $res, $http_code ) = $this->sendMultiNotifications( $url , $signature_key , $multi_notifications_per_partner );
			
			if ( ! $this->updateMultiNotificationStatus( $multi_notifications_per_partner , $http_code , $res ) )
			{
				// because the result_ok is false - send an alert 
				$this->alertAdmin ( $partner , $multi_notifications_per_partner , $params_sent , $res );
			}
		}
		
		// TODO - see if can reduce number of notifications 
		// if an object was deleted - all previous notifications are not relevant
		foreach ( $single_notifications as $not )
		{
			$partner = $not->getPartner();
			if ( !$partner )
			{
				TRACE ( "Error. notification [{$not->getId()}] of type [{$not->getType()}] has a partner_id [$partner_id] but no such partner in the system.");
				continue;
			}

			TRACE ( "Sending single-notifications to partner [{$partner->getId()}]" );
			// we assume that the partner wants notificatins or else it would have not appeared in the DB			
			list ( $url , $signature_key ) = myNotificationMgr::getPartnerNotificationInfo ( $partner );
			list ( $params_sent , $res, $http_code ) = $this->sendNotification ( $url , $signature_key , $not );
			
			
			if ( ! $this->updateNotificationStatus( $not , $http_code , $res ) )
			{
				// because the result_ok is false - send an alert 
				$this->alertAdmin ( $partner , $not , $params_sent , $res );
			}
		}
	}
 
	public function sendNotification ( $url , $signature_key , notification $not , $prefix = null )
	{
		$start_time = microtime (true );
		
		list ( $params , $raw_siganture ) = myNotificationMgr::prepareNotificationData ( $url , $signature_key , $not , $prefix );
				
		try 
		{
			list ( $params , $result, $http_code ) = $this->send ( $url , $params );
		}
		catch ( Exception $ex )
		{
			// try a second time - the connection will probably be closed
			list ( $params , $result, $http_code ) = $this->send ( $url , $params );
		}
		
		$end_time = microtime (true );
		TRACE ( "partner [{$not->getPartnerId()}] notification [{$not->getId()}] of type [{$not->getType()}] to [{$url}]\nhttp result code [{$http_code}]\n" . print_r ( $params , true ) . "\nresult [{$result}]\nraw_signature [$raw_siganture]\ntook [" . ( $end_time - $start_time ) . "]" );
		
		// TODO - see if the hit worked properly
		// the hit should return a specific string to indicate a success 
		return array ( $params , $result, $http_code );
	}
	
	
	// this assumes all notifications belong to the same URL (assuming same partner too)
	public function sendMultiNotifications ( $url , $signature_key , array $not_list  )
	{
		$start_time = microtime (true );
		
//TRACE ( "sendMultiNotifications " . print_r ( $not_list , true ));		
		$params = array();
		$index = 1;
		$not_id_str = "";
		foreach ( $not_list as $not )
		{
			$prefix = "not{$index}_";
			list ( $notification_params , $raw_siganture ) = myNotificationMgr::prepareNotificationData ( $url , $signature_key , $not , $prefix );
			$index++;
			$params = array_merge( $params , $notification_params );
			$not_id_str .= $not->getId() . ", ";
		}
				
		$params["multi_notification"] = "true";
		$params["number_of_notifications"] = count ( $not_list );
		
		//the "sig" parameter will be overidden - so eventually only the last will remain
		list ( $params , $raw_siganture ) = myNotificationMgr::signParams( $signature_key , $params )	;	
		try 
		{
			list ( $params , $result, $http_code ) = $this->send ( $url , $params );
		}
		catch ( Exception $ex )
		{
			// try a second time - the connection will probably be closed
			list ( $params , $result, $http_code ) = $this->send ( $url , $params );
		}
		
		$end_time = microtime (true );
		TRACE ( "partner [{$not->getPartnerId()}] notification [$not_id_str] to [{$url}]\nhttp result code [{$http_code}]\n" . print_r ( $params , true ) . "\nresult [{$result}]\nraw_signature [$raw_siganture]\ntook [" . ( $end_time - $start_time ) . "]" );
		
		// TODO - see if the hit worked properly
		// the hit should return a specific string to indicate a success 
		return array ( $params , $result, $http_code );		
	}
	
	/**
	 * will split the notifications into 2 lists -
	 * multi_notifications_per_partner - an associative array where the key is the partner_id and value is an array of notifications for that partner
	 * single_notifications - an array of notifications that should be sent one by one
	 */
	private function splitToMulti ( $notifications )
	{
		$single_notifications = array();
		$multi_notifications = array();
		foreach ( $notifications as $not )
		{
			$parter_id = $not->getPartnerId();
			$partner = $not->getPartner();
			if ( $partner->getAllowMultiNotification() )
			{
				$multi_notifications_per_partner = @$multi_notifications[$parter_id];
				if ( ! $multi_notifications_per_partner )
				{
					$multi_notifications[$parter_id] = array();			
				}
				$multi_notifications[$parter_id][]= $not;
			}
			else
			{
				$single_notifications [] = $not;
			}
		}
		return array ( $single_notifications , $multi_notifications );  
	}
	
	private function updateMultiNotificationStatus ( array $not_list , $http_code , $res )
	{
		$result_ok = true;
		foreach ( $not_list as $not )
		{
			if ( ! $this->updateNotificationStatus( $not , $http_code , $res ) ) $result_ok = false;
		}
		
		return $result_ok;
	}
	
	private function updateNotificationStatus ( $not , $http_code , $res )
	{
		$not->setNotificationResult ( $res );
		
		$result_ok = true;
		if ( $http_code == 200 && $res !== FALSE && $res == kNotificationJobData::NOTIFICATION_RESULT_OK )
		{
			$not->setStatus ( BatchJob::BATCHJOB_STATUS_FINISHED );
		}
		elseif ( $res == kNotificationJobData::NOTIFICATION_RESULT_ERROR_NO_RETRY )
		{
			$not->setStatus ( BatchJob::BATCHJOB_STATUS_FAILED );
		}
		else //if ( $res == notification::NOTIFICATION_RESULT_ERROR_RETRY )
		{
			$max_send_attempts = $this->m_config->get ( "max_send_attempts" );
			$number_of_attempts = $not->getNumberOfAttempts();
			if ( $number_of_attempts < $max_send_attempts )		
			{
				$not->setNumberOfAttempts($number_of_attempts+1);
				$not->setStatus ( BatchJob::BATCHJOB_STATUS_RETRY );	
				kNotificationJobData::addIndicator($not->getId());
			}
			else
			{
				$not->setStatus ( BatchJob::BATCHJOB_STATUS_FAILED );
				// TODO - send email to Partner & to Kaltura Support
				$result_ok = false ;
				
			}
		}

		$not->save();	

		return $result_ok ;
	}
	
	public function send ( $url , $params )
	{
		// TODO - simulate what once happend
//		$this->closeConnection();
		
		static $close_count = 0;
		
		// once every some time - close the connection and reconnect
		if ( $close_count > 50 )
		{ 
			$this->closeConnection();
			$close_count = 0; 
		}
		
		$close_count++;

		if ( !self::$curl ) self::$curl = curl_init( );
		$ch = self::$curl;
		
		try
		{
			//TRACE ( "-- Hitting URL: $url" );
			$header = array(
				"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", 
				"Accept-Language: en-us,en;q=0.5",
				"Accept-Encoding: gzip,deflate" ,  
				"Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7", 
				"Keep-Alive: 300", 
				"Connection: keep-alive" ,
			 );
			  
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( $params , null , "&" ));
 
// 			curl_setopt($ch, CURLOPT_HTTPHEADER,$header); 
			curl_setopt($ch, CURLOPT_URL, $url );

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, '');
			curl_setopt($ch, CURLOPT_TIMEOUT, 10 );
//			curl_setopt($ch, CURLOPT_HEADER , true );

//			curl_setopt($ch, CURLOPT_VERBOSE, true );
//			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
			curl_setopt( $ch , CURLOPT_FOLLOWLOCATION, TRUE);
			
			$result = curl_exec($ch);
			
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		}
		catch ( Exception $ex )
		{
			$this->closeConnection();
			throw $ex;		
		}

		return array ( $params , $result, $http_code );
	}
	
	private function closeConnection ()
	{
		TRACE ( "Closing connection" );
		if ( self::$curl != null)
		curl_close(self::$curl);
		self::$curl = null;
	}
	
	
	protected function alertAdmin ( Partner $partner , $not , $params_sent , $response )
	{
		if ( !$not || is_array( $not ) ) return;
		TRACE ( "Error with notification [{$not->getId()}]");
		$admin_email = $partner->getAdminEmail();
		if ( $admin_email )
		{
			$this->sendAlert( $admin_email , $partner , $not , $params_sent , $response );
		}
		
		$kaltura_support = $this->m_config->get ( "kaltura_notification_support_email" );
		$this->sendAlert( $kaltura_support , $partner , $not , $params_sent , $response );
	}
	
	protected function sendAlert ( $recipient , Partner $partner , $not , $params_sent , $response ) 
	{
		if ( !$not || is_array( $not ) ) return;
		TRACE ( "Error with notification [{$not->getId()}] sent to recipient [$recipient]");
		$admin_name = $partner->getAdminName();
		$admin_email = $partner->getAdminEmail();
	  	$mailjob = new MailJob();
	 	$mailjob->Initialize( self::KALTURA_NOTIFICAITON_ERROR_EMAIL );
 		$mailjob->setFromEmail( kConf::get ( "batch_notification_sender_email" ) ) ;//'notifications@kaltura.com');
	 	$mailjob->setFromName( kConf::get ( "batch_notification_sender_name" ) ) ;//'Kaltura');
	 	// send the $cms_email,$cms_password, TWICE !
 		$mailjob->setBodyParamsArray( array($admin_name, $admin_email , $not->getId(), $partner->getUrl2() , $response , print_r ( $params_sent , true ) ) );
		$mailjob->setSubjectParamsArray( array($not->getId()) );
		$mailjob->setRecipientEmail( $recipient ); // here the recipient will be one of the 2: $admin_email or the kaltura support
		$mailjob->save();		
	}
	
	public function writeToLog( $message )
	{
		TRACE ( $message );
	}
}

?>