<?php
/**
 * @package Scheduler
 * @subpackage Mailer
 */
require_once("bootstrap.php");

/**
 * Will import a single URL and store it in the file system.
 * The state machine of the job is as follows:
 * 	 	parse URL	(youTube is a special case) 
 * 		fetch heraders (to calculate the size of the file)
 * 		fetch file (update the job's progress - 100% is when the whole file as appeared in the header)
 * 		move the file to the archive
 * 		set the entry's new status and file details  (check if FLV) 
 *
 * @package Scheduler
 * @subpackage Mailer
 */
class KAsyncMailer extends KBatchBase
{
	/**
	 * @return number
	 */
	public static function getType()
	{
		return KalturaBatchJobType::MAIL;
	}
	
	protected function init()
	{
		$this->saveQueueFilter(self::getType());		
	}
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job){}
	protected function freeExclusiveJob(KalturaBatchJob $job){}
	
	const MAILER_DEFAULT_SENDER_EMAIL = 'notifications@kaltura.com';
	const MAILER_DEFAULT_SENDER_NAME = 'Kaltura Notification Service';
	
	// TODO - replace email config mechanism !!
	protected $texts_array; // will hold the configuration of the in file
	
	/**
	 * @var PHPMailer
	 */
	protected $mail;
	
	public function run()
	{
		KalturaLog::info("Mail batch is running");
		
		if($this->taskConfig->isInitOnly())
			return $this->init();
		
		$jobs = $this->kClient->batch->getExclusiveMailJobs( 
			$this->getExclusiveLockKey() , 
			$this->taskConfig->maximumExecutionTime , 
			$this->taskConfig->maxJobsEachRun , 
			$this->getFilter());
			
		KalturaLog::info(count($jobs) . " mail jobs to perform");
								
		if(!count($jobs) > 0)
		{
			KalturaLog::info("Queue size: 0 sent to scheduler");
			$this->saveSchedulerQueue(self::getType());
			return;
		}
				
		$this->initConfig();
		$this->kClient->startMultiRequest();
		foreach($jobs as $job)
			$this->send($job, $job->data);
		$this->kClient->doMultiRequest();		
			
			
		$this->kClient->startMultiRequest();
		foreach($jobs as $job)
		{
			KalturaLog::info("Free job[$job->id]");
			$this->onFree($job);
	 		$this->kClient->batch->freeExclusiveMailJob($job->id, $this->getExclusiveLockKey());
		}
		$responses = $this->kClient->doMultiRequest();
		$response = end($responses);
		
		KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
		$this->saveSchedulerQueue(self::getType(), $response->queueSize);
	}
	
	/*
	 * Will take a single KalturaMailJob and send the mail using PHPMailer  
	 * 
	 * @param KalturaBatchJob $job
	 * @param KalturaMailJobData $data
	 */
	protected function send(KalturaBatchJob $job, KalturaMailJobData $data)
	{
		KalturaLog::debug("send($job->id)");
		
		try
		{
 			$result = $this->sendEmail( 
 				$data->recipientEmail,
 				$data->recipientName,
 				$data->mailType,
 				explode ( "|" , $data->subjectParams ) ,
 				explode ( "|" , $data->bodyParams ),
 				$data->culture!= ''? $data->culture : 'en',
 				$data->fromEmail ,
 				$data->fromName,
 				$data->isHtml);
			
	 		if ( $result )
	 		{
	 			$job->status = KalturaBatchJobStatus::FINISHED;
	 		}
	 		else
	 		{
	 			$job->status = KalturaBatchJobStatus::FAILED;
	 		}
	 			
			KalturaLog::info("job[$job->id] status: $job->status");
			$this->onUpdate($job);
			
			$updateJob = new KalturaBatchJob();
			$updateJob->status = $job->status;
	 		$this->kClient->batch->updateExclusiveMailJob($job->id, $this->getExclusiveLockKey(), $updateJob);			
		}
		catch ( Exception $ex )
		{
			KalturaLog::crit( $ex );
		}
	}
	

	protected function sendEmail( $recipientemail, $recipientname, $type, $subjectParams, $bodyParams, $culture, $fromemail , $fromname, $isHtml = false  )
	{
		KalturaLog::debug(__METHOD__ . "($recipientemail, $recipientname, $type, $subjectParams, $bodyParams, $culture, $fromemail , $fromname)");
		
		$this->mail = new PHPMailer();
		$this->mail->CharSet = 'utf-8';
		$this->mail->IsHTML($isHtml);
		$this->mail->AddAddress($recipientemail);
			
		if ( $fromemail != null && $fromemail != '' ) 
		{
			// the sender is what was definied before the template mechanism
			$this->mail->Sender = self::MAILER_DEFAULT_SENDER_EMAIL;
			
			$this->mail->From = $fromemail ;
			$this->mail->FromName = ( $fromname ? $fromname : $fromemail ) ;
		}
		else
		{
			$this->mail->Sender = self::MAILER_DEFAULT_SENDER_EMAIL;
			
			$this->mail->From = self::MAILER_DEFAULT_SENDER_EMAIL ;
			$this->mail->FromName = self::MAILER_DEFAULT_SENDER_NAME ;
		}
			
		$this->mail->Subject = $this->getSubjectByType( $type, $culture, $subjectParams  ) ;
		$this->mail->Body = $this->getBodyByType( $type, $culture, $bodyParams, $recipientemail, $isHtml ) ;
			
//		$this->mail->setContentType( "text/plain; charset=\"utf-8\"" ) ; //; charset=utf-8" );
		// definition of the required parameters
		
//		$this->mail->prepare();

		// send the email
		$body = $this->mail->Body;
		if ( strlen ( $body ) > 1000 ) 
		{
			$body_to_log = "total length [" . strlen ( $body ) . "]:\n" . " body: " . substr($body , 0 , 1000 ) ;
		}
		else
		{
			$body_to_log  = " body: " . $body;
		}
		KalturaLog::info( 'sending email to: '. $recipientemail . " subject: " . $this->mail->Subject .  $body_to_log );
			
		try
		{
			return ( $this->mail->Send() ) ;
		} 
		catch ( Exception $e )
		{
			KalturaLog::err( $e );
			return false;
		}
	}
	
	
	public function getSubjectByType( $type, $culture, $subjectParamsArray  )
	{
		KalturaLog::debug(__METHOD__ . "($type, $culture, $subjectParamsArray)");
		
		if ( $type > 0 )
		{
			$subject = $this->texts_array[$culture]['subjects'][$type];
			$subject = vsprintf( $subject, $subjectParamsArray );
			//$this->mail->setSubject( $subject );
			return $subject;
		}
		else
		{
			// use template 
		}
	}

	public function getBodyByType( $type, $culture, $bodyParamsArray, $recipientemail, $isHtml = false  )
	{
		KalturaLog::debug(__METHOD__ . "($type, $culture, $bodyParamsArray, $recipientemail)");

		// if this does not need the common_header, under common_text should have $type_header =
		// same with footer
		$common_taxt_arr = $this->texts_array[$culture]['common_text'];
		$footer = ( isset($common_taxt_arr[$type . '_footer']) ) ? $common_taxt_arr[$type . '_footer'] : $common_taxt_arr['footer'];
		$body = $this->texts_array[$culture]['bodies'][$type];

		$footer = vsprintf( $footer, array( $recipientemail , self::createBlockEmailStr( $recipientemail ) ) );

		$body .= "\n" . $footer;
		KalturaLog::debug( __METHOD__ . " Debug: type [$type]\n " . print_r ( $bodyParamsArray , true ) );
		$body = vsprintf( $body, $bodyParamsArray );
		if ($isHtml)
			$body = str_replace( "<BR>", "<br />", $body );
		else
			$body = str_replace( "<BR>", chr(13).chr(10), $body );
			
		$body = str_replace( "<EQ>", "=", $body );
		$body = str_replace( "<EM>", "!", $body ); // exclamation mark
		
		return $body;
	}
		
	protected function initConfig ( )
	{
		KalturaLog::debug(__METHOD__ . "()");
		$cultures = array( 'en' );

		// now we read the ini files with the texts
		// NOTE: '=' signs CANNOT be used inside the ini files, instead use "<EQ>"
		$rootdir =  realpath(dirname(__FILE__).'');
			
		foreach ( $cultures as $culture)
		{
			$filename = $rootdir."/emails_".$culture.".ini";
			KalturaLog::debug( 'ini filename = '.$filename );
			if ( ! file_exists ( $filename )) 
			{
				KalturaLog::crit( 'Fatal:::: Cannot find file: '.$filename );
				die();
			}
			$ini_array = parse_ini_file( $filename, true );
			$this->texts_array[$culture] = array( 'subjects' => $ini_array['subjects'],
			'bodies'=>$ini_array['bodies'] ,
			'common_text'=> $ini_array['common_text'] );
		}		
	}
	
	
	// should be the same as on the server
	protected static $key = "myBlockedEmailUtils";
	const SEPARATOR = ";";
	const EXPIRY_INTERVAL = 2592000; // 30 days in seconds
	
	public static function createBlockEmailStr ( $email )
	{
		KalturaLog::debug(__METHOD__ . "($email)");
		return  $email . self::SEPARATOR . kString::expiryHash( $email , self::$key , self::EXPIRY_INTERVAL );
	}
}
?>