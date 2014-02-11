<?php
/**
 * Will import a single URL and store it in the file system.
 * The state machine of the job is as follows:
 * 	 	parse URL	(youTube is a special case) 
 * 		fetch heraders (to calculate the size of the file)
 * 		fetch file 
 * 		move the file to the archive
 * 		set the entry's new status and file details  (check if FLV) 
 *
 * @package Scheduler
 * @subpackage Mailer
 */
class KAsyncMailer extends KJobHandlerWorker
{
	const MAILER_DEFAULT_SENDER_EMAIL = 'notifications@kaltura.com';
	const MAILER_DEFAULT_SENDER_NAME = 'Kaltura Notification Service';
	const DEFAULT_LANGUAGE = 'en';
	
	protected $texts_array; // will hold the configuration of the ini file
	
	/**
	 * @var PHPMailer
	 */
	protected $mail;
	
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::MAIL;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $job;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::run()
	 */
	public function run($jobs = null)
	{
		KalturaLog::info("Mail batch is running");
		
		if(KBatchBase::$taskConfig->isInitOnly())
			return $this->init();
		
		$jobs = KBatchBase::$kClient->batch->getExclusiveJobs( 
			$this->getExclusiveLockKey() , 
			KBatchBase::$taskConfig->maximumExecutionTime , 
			$this->getMaxJobsEachRun() , 
			$this->getFilter(),
			static::getType()
		);
			
		KalturaLog::info(count($jobs) . " mail jobs to perform");
								
		if(!count($jobs) > 0)
		{
			KalturaLog::info("Queue size: 0 sent to scheduler");
			$this->saveSchedulerQueue(self::getType());
			return;
		}
				
		$this->initConfig();
		KBatchBase::$kClient->startMultiRequest();
		foreach($jobs as $job)
			$this->send($job, $job->data);
		KBatchBase::$kClient->doMultiRequest();		
			
			
		KBatchBase::$kClient->startMultiRequest();
		foreach($jobs as $job)
		{
			KalturaLog::info("Free job[$job->id]");
			$this->onFree($job);
	 		KBatchBase::$kClient->batch->freeExclusiveJob($job->id, $this->getExclusiveLockKey(), static::getType());
		}
		$responses = KBatchBase::$kClient->doMultiRequest();
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
		
		if (!isset($this->texts_array[$data->language]))
		{
			$this->initConfig($data->language);	
		}
		
		try
		{
			$separator = $data->separator;
 			$result = $this->sendEmail( 
 				$data->recipientEmail,
 				$data->recipientName,
 				$data->mailType,
 				explode ( $separator , $data->subjectParams ) ,
 				explode ( $separator , $data->bodyParams ),
 				$data->fromEmail ,
 				$data->fromName,
 				$data->language,
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
	 		KBatchBase::$kClient->batch->updateExclusiveJob($job->id, $this->getExclusiveLockKey(), $updateJob);			
		}
		catch ( Exception $ex )
		{
			KalturaLog::crit( $ex );
		}
	}
	

	protected function sendEmail( $recipientemail, $recipientname, $type, $subjectParams, $bodyParams, $fromemail , $fromname, $language = 'en', $isHtml = false  )
	{
		KalturaLog::debug(__METHOD__ . "($recipientemail, $recipientname, $type, $subjectParams, $bodyParams, $language, $fromemail , $fromname)");
		
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
			
		$this->mail->Subject = $this->getSubjectByType( $type, $language, $subjectParams  ) ;
		$this->mail->Body = $this->getBodyByType( $type, $language, $bodyParams, $recipientemail, $isHtml ) ;
			
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
	
	
	protected function getSubjectByType( $type, $language, $subjectParamsArray  )
	{
		KalturaLog::debug(__METHOD__ . "($type, $language, $subjectParamsArray)");
		
		if ( $type > 0 )
		{
			$languageTexts = isset($this->texts_array[$language]) ? $this->texts_array[$language] : reset($this->texts_array);
			$defaultLanguageTexts = $this->texts_array[self::DEFAULT_LANGUAGE];
			$subject = isset ($languageTexts['subjects'][$type]) ? $languageTexts['subjects'][$type] : $defaultLanguageTexts['subjects'][$type];
			$subject = vsprintf( $subject, $subjectParamsArray );
			//$this->mail->setSubject( $subject );
			return $subject;
		}
		else
		{
			// use template 
		}
	}

	protected function getBodyByType( $type, $language, $bodyParamsArray, $recipientemail, $isHtml = false  )
	{
		KalturaLog::debug(__METHOD__ . "($type, $language, $bodyParamsArray, $recipientemail)");

		// if this does not need the common_header, under common_text should have $type_header =
		// same with footer
		$languageTexts = isset($this->texts_array[$language]) ? $this->texts_array[$language] : reset($this->texts_array);
		$defaultLanguageTexts = $this->texts_array[self::DEFAULT_LANGUAGE];
		$common_text_arr = $languageTexts['common_text'];
		$defaultCommonTexts = $defaultLanguageTexts['common_text'];
		$footer = ( isset($common_text_arr[$type . '_footer']) ) ? $common_text_arr[$type . '_footer'] : ($common_text_arr['footer'] ? $common_text_arr['footer'] : $defaultCommonTexts['footer']);
		$body = isset($languageTexts['bodies'][$type]) ? $languageTexts['bodies'][$type] : $defaultLanguageTexts['bodies'][$type];
		
		// TODO - move to batch config
		$forumsLink = $this->getAdditionalParams('forumUrl');
		$unsubscribeLink = $this->getAdditionalParams('unsubscribeUrl') . self::createBlockEmailStr($recipientemail);
		
		$footer = vsprintf($footer, array($forumsLink, $unsubscribeLink) );

		$body .= "\n" . $footer;
		KalturaLog::debug("type [$type]");
		KalturaLog::debug("params [" . print_r($bodyParamsArray, true) . "]");
		KalturaLog::debug("body [$body]");
		KalturaLog::debug("footer [$footer]");
		$body = vsprintf( $body, $bodyParamsArray );
		if ($isHtml)
		{
			$body = str_replace( "<BR>", "<br />\n", $body );
			$body = '<p align="left" dir="ltr">'.$body.'</p>';
		}
		else
		{
			$body = str_replace( "<BR>", chr(13).chr(10), $body );
		}	
		$body = str_replace( "<EQ>", "=", $body );
		$body = str_replace( "<EM>", "!", $body ); // exclamation mark
		
		KalturaLog::debug("final body [$body]");
		return $body;
	}
		
	protected function initConfig ( $language = null)
	{
		KalturaLog::debug(__METHOD__ . "()");
		$languages = array($language ? $language : self::DEFAULT_LANGUAGE );

		// now we read the ini files with the texts
		// NOTE: '=' signs CANNOT be used inside the ini files, instead use "<EQ>"
		$rootdir =  realpath(dirname(__FILE__).'');
			
		foreach ( $languages as $language)
		{
			if (!isset($this->texts_array[$language]))
			{
				$filename = $rootdir."/emails_".$language.".ini";
				KalturaLog::debug( 'ini filename = '.$filename );
				if ( ! file_exists ( $filename )) 
				{
					KalturaLog::crit( 'Fatal:::: Cannot find file: '.$filename );
					continue;
				}
				$ini_array = parse_ini_file( $filename, true );
				$this->texts_array[$language] = array( 'subjects' => $ini_array['subjects'],
				'bodies'=>$ini_array['bodies'] ,
				'common_text'=> $ini_array['common_text'] );
			}
		}		
	}
	
	
	// should be the same as on the server
	protected static $key = "myBlockedEmailUtils";
	const SEPARATOR = ";";
	const EXPIRY_INTERVAL = 2592000; // 30 days in seconds
	
	protected static function createBlockEmailStr ( $email )
	{
		KalturaLog::debug(__METHOD__ . "($email)");
		return  $email . self::SEPARATOR . kString::expiryHash( $email , self::$key , self::EXPIRY_INTERVAL );
	}
}
