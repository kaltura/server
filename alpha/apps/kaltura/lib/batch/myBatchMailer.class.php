<?php
require_once( 'myBatchBase.class.php');

class myBatchMailer extends myBatchBase
{

	const MAILER_DEFAULT_SENDER_EMAIL = 'notifications@kaltura.com';
	const MAILER_DEFAULT_SENDER_NAME = 'Kaltura Notification Service';

	const MAILER_MAX_PER_QUERY = 100;

	protected $mail;

	// will be used to store default message subjects and bodies
	private $texts_array;



	public function __construct( $script_name )
	{
		$this->script_name = $script_name;
		$this->register( $script_name );
		
		$this->mail = new sfMail();
		$this->mail->initialize();
		$this->mail->setMailer('sendmail');
		$this->mail->setCharset('utf-8');
//		$this->mail->addCustomHeader( "X-Mailer" , "" );
		
		// definition of the required parameters
		$this->mail->setSender( myBatchMailer::MAILER_DEFAULT_SENDER_EMAIL, myBatchMailer::MAILER_DEFAULT_SENDER_NAME );
		$this->mail->setFrom( myBatchMailer::MAILER_DEFAULT_SENDER_EMAIL, myBatchMailer::MAILER_DEFAULT_SENDER_NAME );

		$cultures = array( 'en' );

		// now we read the ini files with the texts
		// NOTE: '=' signs CANNOT be used inside the ini files, instead use "<EQ>"
		$rootdir =  realpath(dirname(__FILE__).'/../..');
			
		foreach ( $cultures as $culture)
		{
			$filename = $rootdir."/config/emails_".$culture.".ini";
			$this->writeToLog( 'ini filename = '.$filename );
			if ( ! file_exists ( $filename )) 
			{
				$this->writeToLog( 'Fatal:::: Cannot find file: '.$filename );
				die();
			}
			$ini_array = parse_ini_file( $filename, true );
			$this->texts_array[$culture] = array( 'subjects' => $ini_array['subjects'],
			'bodies'=>$ini_array['bodies'] ,
			'common_text'=> $ini_array['common_text'] );

			echo ( "All data:\n" );
			print_r ( $this->texts_array[$culture] );
		}
	}

	// TODO - implement call to mail_job table
	public static function getBatchStatus( $args )	
	{	
		$batch_status = new batchStatus();
		$batch_status->batch_name = $args[0];
		$stats = self::getJobStats( 2400 );
		$batch_status->addToPending( "DB:mail_job, status=" . MailJobPeer::MAIL_STATUS_PENDING ,
			@$stats[MailJobPeer::MAIL_STATUS_PENDING]["count"]);
		$batch_status->addToInProc( "" ,  0 );//@$stats["full_stats"][BatchJob::BATCHJOB_STATUS_PROCESSING]["count"];
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
	    	"from mail_job  where status in (1) and created_at>\"$from_date\" " . 
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

	    	// foreach job_type - creaet an array per status
	    	$notification_stats [$status] = array ( "oldest" => $oldest , "newest" => $newest , "count" => $count ) ;
	    }

		return $notification_stats;
				
	}	
	public function setSubjectByType( $type, $culture, $subjectParamsArray  )
	{
		if ( $type > 0 )
		{
			$subject = $this->texts_array[$culture]['subjects'][$type];
			$subject = vsprintf( $subject, $subjectParamsArray );
			$this->mail->setSubject( $subject );
		}
		else
		{
			// use template 
		}
	}

	public function setBodyByType( $type, $culture, $bodyParamsArray, $recipientemail   )
	{

		// if this does not need the common_header, under common_text should have $type_header =
		// same with footer
		$common_taxt_arr = $this->texts_array[$culture]['common_text'];

		//$place_header = @$common_taxt_arr[$type . '_header'] ;
		$place_footer = @$common_taxt_arr[$type . '_footer'] ;

		//$header = ( $place_header != "" ) ? $place_header : $common_taxt_arr['header'];
		$footer = ( $place_footer != "" ) ? $place_footer : $common_taxt_arr['footer'];


		//$body = $header . "\n";
		$body = $this->texts_array[$culture]['bodies'][$type];

		$footer = vsprintf( $footer, array( $recipientemail , myBlockedEmailUtils::createBlockEmailStr( $recipientemail ) ) );

		$body .= "\n" . $footer;
TRACE ( __METHOD__ . " Debug: type [$type]\n " . print_r ( $bodyParamsArray , true ) );
		$body = vsprintf( $body, $bodyParamsArray );
		$body = str_replace( "<BR>", chr(13).chr(10), $body );
		$body = str_replace( "<EQ>", "=", $body );
		$body = str_replace( "<EM>", "!", $body ); // exclamation mark
		$this->mail->setBody( $body );

	}

	public function sendEmail( $recipientemail, $recipientname, $type, $subjectParams, $bodyParams, $culture, $fromemail , $fromname  )
	{

		$this->mail->clearAllRecipients();
		$this->mail->addAddress($recipientemail);
			
		if ( $fromemail != null && $fromemail != '' ) 
		{
			// the sender is what was definied before the template mechanism
			$this->mail->setSender( myBatchMailer::MAILER_DEFAULT_SENDER_EMAIL, myBatchMailer::MAILER_DEFAULT_SENDER_NAME );
			$this->mail->setFrom( $fromemail , ( $fromname ? $fromname : $fromemail ) );
		}
		else
		{
			$this->mail->setSender( myBatchMailer::MAILER_DEFAULT_SENDER_EMAIL, myBatchMailer::MAILER_DEFAULT_SENDER_NAME );
			$this->mail->setFrom( myBatchMailer::MAILER_DEFAULT_SENDER_EMAIL, myBatchMailer::MAILER_DEFAULT_SENDER_NAME );
		}
			
		$this->setSubjectByType( $type, $culture, $subjectParams  );
		$this->setBodyByType( $type, $culture, $bodyParams, $recipientemail  );
			
		$this->mail->setContentType( "text/plain; charset=\"utf-8\"" ) ; //; charset=utf-8" );
		// definition of the required parameters
		
		$this->mail->prepare();

		// send the email
		$body = $this->mail->getBody();
		if ( strlen ( $body ) > 1000 ) 
		{
			$body_to_log = "total length [" . strlen ( $body ) . "]:\n" . " body: " . substr($body , 0 , 1000 ) ;
		}
		else
		{
			$body_to_log  = " body: " . $body;
		}
		$this->writeToLog( 'sending email to: '. $recipientemail . " subject: " . $this->mail->getSubject() .  $body_to_log );
		//$this->writeToLog( 'subject: '. $this->mail->getSubject() );
		//$this->writeToLog( 'body: '. $this->mail->getBody() );
			
		try
		{
			return ( $this->mail->send() ) ;
		} catch ( Exception $e )
		{
			$this->writeToLog( $e );
			//TODO: handle this error
			return true;
		}
	}


	// in this case the template will be used - the params are placed only in the body_parms field.
	public function sendEmailFromTemplate ( MailJob $mail_job )
	{
		$this->mail->clearAllRecipients();
		$this->mail->addAddress($mail_job->getRecipientEmail() );

		$this->mail->setContentType( "text/html") ; //; charset=utf-8" );
//		$this->mail->setSender( $mail_job->getFromName() );
//		$this->mail->setFrom( $mail_job->getFromEmail() );
		
		
		if ( $mail_job->getFromEmail() )
		{
			  $this->mail->setFrom( $mail_job->getFromEmail() , ( $mail_job->getFromName() ? $mail_job->getFromName() : $mail_job->getFromEmail() ) );
		}	

		$params_str = $mail_job->getBodyParams();
		$params = baseObjectUtils::arrayFromString ( $params_str );
		
		list ( $subject , $body ) = myTemplateUtils::replaceMarkupByParts ( $mail_job->getTemplatePath() , $params  );

		$this->mail->setSubject ( $subject );
		$this->mail->setBody ( $body );
			
		$this->mail->prepare();

		// send the email
			$body = $this->mail->getBody();
		if ( strlen ( $body ) > 1000 ) 
		{
			$body_to_log = "total lenght [" . strlen ( $body ) . "]:\n" . substr($body , 0 , 300 ) ;
		}
		else
		{
			$body_to_log  = $body;
		}
		$this->writeToLog( 'sending email based on template [' . $mail_job->getTemplatePath() . '] to: '. $mail_job->getRecipientEmail() . "\nsubject: " . $this->mail->getSubject() . "\nbody:\n" . $body_to_log );
		//$this->writeToLog( 'subject: '. $this->mail->getSubject() );
		//$this->writeToLog( 'body: '. $this->mail->getBody() );
			
		try
		{
			return ( $this->mail->send() ) ;
		} catch ( Exception $e )
		{
			$this->writeToLog( $e );
			//TODO: handle this error
			return true;
		}		
	}
	
	public function doMailLoop()
	{
		list ( $sleep_between_cycles ,
		$number_of_times_to_skip_writing_sleeping ) = self::getSleepParams( 'app_mailer_' );
			
		self::initDb();

		$temp_count = 0;

		while( true )
		{
			self::exitIfDone();
			try
			{
				$c = new Criteria();

				$date_criterion = $c->getNewCriterion( MailJobPeer::MIN_SEND_DATE, time(), Criteria::LESS_THAN ) ;
				$date_criterion->addOr ( $c->getNewCriterion( MailJobPeer::MIN_SEND_DATE, null, Criteria::EQUAL ) ) ;
				
				$c->add($date_criterion);
				$c->add(MailJobPeer::STATUS, MailJobPeer::MAIL_STATUS_PENDING );
				$c->addAscendingOrderByColumn( MailJobPeer::MAIL_PRIORITY );
				$c->setLimit( self::MAILER_MAX_PER_QUERY );

				$c->addAnd ( MailJobPeer::DC , kDataCenterMgr::getCurrentDcId() );
	 			$mailjobs = MailJobPeer::doSelect( $c );
	 			
	 			$c->clear();
	 			
	 			foreach ( $mailjobs as $mailjob )
	 			{
	 				if ( $mailjob->getMailType() == 0 )
	 				{
	 					$result = $this->sendEmailFromTemplate ( $mailjob );
	 				}
	 				else
	 				{
	 					$result = $this->sendEmail( $mailjob->getRecipientEmail(),
	 						$mailjob->getRecipientName(),
			 				$mailjob->getMailType(),
			 				$mailjob->getSubjectParamsArray(),
			 				$mailjob->getBodyParamsArray(),
			 				$mailjob->getCulture() != ''? $mailjob->getCulture() : 'en',
			 				$mailjob->getFromEmail() ,
			 				$mailjob->getFromName() );
	 				}	 				
	 				if ( $result  )
	 				{
	 					$mailjob->delete();
	 					$this->writeToLog( "Deleted mail_job [". $mailjob->getId(). "]");
	 				}
	 				else
	 				{
	 					$this->writeToLog( "Error sending mail_job [". $mailjob->getId(). "]");
	 					$mailjob->setStatus( MailJobPeer::MAIL_STATUS_ERROR );
	 					$mailjob->save();
	 				}
	 			}
	
	 			if ( $temp_count == 0 )
	 			{
	 				TRACE ( "Sent all emails. sleeping for a while (" . $sleep_between_cycles .
						" seconds). Will write to the log in (" . ( $sleep_between_cycles * $number_of_times_to_skip_writing_sleeping ) . ") seconds" );
	 			}
	 				
	 			$temp_count++;
	 			if ($temp_count >= $number_of_times_to_skip_writing_sleeping ) $temp_count = 0;
	 				
	 			self::succeeded();
	 			sleep ( $sleep_between_cycles );

			}
			catch ( Exception $ex )
			{
				TRACE ( "ERROR: " . $ex->getMessage() );
				self::initDb( true );
				self::failed();
			}
		}


			
	}
	
	public function writeToLog( $message )
	{
		TRACE ( $message );
	}

}

?>