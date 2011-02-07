<?php

/**
 * Subclass for representing a row from the 'alert' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class alert extends Basealert
{
	const GENERAL_ALERT_TYPE_STF = 100;
	
	const GENERAL_ALERT_TYPE_NEWSLETTER = 10;
	const GENERAL_ALERT_TYPE_FAVORITED_ME = 11;
	const GENERAL_ALERT_TYPE_FAVORITED_MY_CLIP = 12;
	const GENERAL_ALERT_TYPE_COMMENT_ADDED = 13;
	
	const KALTURAS_PRODUCED_ALERT_TYPE_CONTRIB_ADDED = 20;
	const KALTURAS_PRODUCED_ALERT_TYPE_SUBSCRIBER_ADDED = 21;
	const KALTURAS_PRODUCED_ALERT_TYPE_ROUGHCUT_CREATED = 22;
	const KALTURAS_PRODUCED_ALERT_TYPE_FAVORITED = 23;
	const KALTURAS_PRODUCED_ALERT_TYPE_COMMENT_ADDED = 24;
	
	const KALTURAS_PARTOF_ALERT_TYPE_CONTRIB_ADDED = 30;
	const KALTURAS_PARTOF_ALERT_TYPE_ROUGHCUT_CREATED = 31;
	const KALTURAS_PARTOF_ALERT_TYPE_FAVORITED = 32;
	const KALTURAS_PARTOF_ALERT_TYPE_COMMENT_ADDED = 33;
	
	const KALTURAS_SUBSCRIBEDTO_ALERT_TYPE_CONTRIB_ADDED = 40;
	const KALTURAS_SUBSCRIBEDTO_ALERT_TYPE_ROUGHCUT_CREATED = 41;
	const KALTURAS_SUBSCRIBEDTO_ALERT_TYPE_FAVORITED = 42;
	const KALTURAS_SUBSCRIBEDTO_ALERT_TYPE_COMMENT_ADDED = 43;
	
	
	private $additionalParamsArray; // space for more params from the calling actions
	private $kuser; //the user receiving the alerts
	
	public function sendEmailAlert()
	{
		$this->kuser = kuserPeer::retrieveByPK( $this->getKuserId() );
		if( $this->kuser  )
		{
			kJobsManager::addMailJob(
				null, 
				0, 
				$this->kuser->getPartnerId(), 
				$this->getAlertType(), 
				kMailJobData::MAIL_PRIORITY_NORMAL, 
				kconf::get ( "batch_notification_sender_email" ), 
				kconf::get ( "batch_notification_sender_name" ), 
				$this->kuser->getEmail(), 
				$this->getBodyParamsArray(), 
				$this->getSubjectParamsArray());
		}
	}

	// this function is used to store more variables that might be needed in the alerts body or subject
	public function setAdditionalParamsArray( $additionalParamsArray )
	{
		$this->additionalParamsArray =  $additionalParamsArray;
	}
	
	private function getSubjectParamsArray()
	{
		$subjectParamsArray = NULL;
		
		switch ( $this->getAlertType() )
		{
			case alert::GENERAL_ALERT_TYPE_FAVORITED_ME: 
				$subjectParamsArray= array ( $this->additionalParamsArray['screenname'] ); 
				break;
			case alert::GENERAL_ALERT_TYPE_FAVORITED_MY_CLIP: 
				$subjectParamsArray= array ( $this->additionalParamsArray['screenname'], $this->additionalParamsArray['entry_name'] ); 
				break;
			case alert::GENERAL_ALERT_TYPE_COMMENT_ADDED: 
				break;
			case alert::KALTURAS_PRODUCED_ALERT_TYPE_CONTRIB_ADDED: 
				$subjectParamsArray= array ( $this->additionalParamsArray['kshow_name'] );
				break;
			case alert::KALTURAS_PRODUCED_ALERT_TYPE_SUBSCRIBER_ADDED: 
				$subjectParamsArray= array ( $this->additionalParamsArray['kshow_name'] );
				break;
			case alert::KALTURAS_PRODUCED_ALERT_TYPE_ROUGHCUT_CREATED: 
				$subjectParamsArray= array ( $this->additionalParamsArray['kshow_name'] );
				break;
			case alert::KALTURAS_PRODUCED_ALERT_TYPE_FAVORITED: 
				$subjectParamsArray= array ( $this->additionalParamsArray['screenname'], $this->additionalParamsArray['kshow_name'] );
				break;
			case alert::KALTURAS_PRODUCED_ALERT_TYPE_COMMENT_ADDED: 
				$subjectParamsArray= array ( $this->additionalParamsArray['kshow_name'] );
				break;
			
			case alert::KALTURAS_PARTOF_ALERT_TYPE_CONTRIB_ADDED: 
				$subjectParamsArray= array ( $this->additionalParamsArray['kshow_name'] );
				break;
			case alert::KALTURAS_PARTOF_ALERT_TYPE_ROUGHCUT_CREATED: 
				$subjectParamsArray= array ( $this->additionalParamsArray['kshow_name'] );
				break;
			case alert::KALTURAS_PARTOF_ALERT_TYPE_FAVORITED: 
				$subjectParamsArray= array ( $this->additionalParamsArray['screenname'], $this->additionalParamsArray['kshow_name'] );
				break;
			case alert::KALTURAS_PARTOF_ALERT_TYPE_COMMENT_ADDED: 
				$subjectParamsArray= array ( $this->additionalParamsArray['kshow_name'] );
				break;
			
			case alert::KALTURAS_SUBSCRIBEDTO_ALERT_TYPE_CONTRIB_ADDED: 
				$subjectParamsArray= array ( $this->additionalParamsArray['kshow_name'] );
				break;
			case alert::KALTURAS_SUBSCRIBEDTO_ALERT_TYPE_ROUGHCUT_CREATED: 
				$subjectParamsArray= array ( $this->additionalParamsArray['kshow_name'] );
				break;
			case alert::KALTURAS_SUBSCRIBEDTO_ALERT_TYPE_FAVORITED: 
				$subjectParamsArray= array ( $this->additionalParamsArray['screenname'], $this->additionalParamsArray['kshow_name'] );
				break;
			case alert::KALTURAS_SUBSCRIBEDTO_ALERT_TYPE_COMMENT_ADDED: 
				$subjectParamsArray= array ( $this->additionalParamsArray['kshow_name'] );
				break;
			
			
		}
		return $subjectParamsArray;
	}
	
	private function getBodyParamsArray()
	{
		$bodyParamsArray = NULL;
		
		// now we're building the array of params based on the additional params and other information we have here
		// these will be integrated in lib/mailjob.php with templates coming from config/email_en.ini using vsprintf 
		switch ( $this->getAlertType() )
		{
			case alert::GENERAL_ALERT_TYPE_FAVORITED_ME: 
				// favorited_username, user who favorited name X 3
				$bodyParamsArray = array ( $this->kuser->getScreenName(), $this->additionalParamsArray['screenname'], $this->additionalParamsArray['screenname'], $this->additionalParamsArray['screenname'] ); 
				break;
			case alert::GENERAL_ALERT_TYPE_FAVORITED_MY_CLIP: 
				// favorited_username, user who favorited name, entry name, user who favorited name, user who favorited name
				$bodyParamsArray = array ( $this->kuser->getScreenName(), $this->additionalParamsArray['screenname'],$this->additionalParamsArray['entry_name'],$this->additionalParamsArray['screenname'], $this->additionalParamsArray['screenname'] );
				break;
			case alert::GENERAL_ALERT_TYPE_COMMENT_ADDED: 
				$bodyParamsArray = array ( $this->kuser->getScreenName(), $this->additionalParamsArray['screenname'],$this->additionalParamsArray['screenname'], $this->additionalParamsArray['screenname'] );
				break;
			case alert::KALTURAS_PRODUCED_ALERT_TYPE_CONTRIB_ADDED: 
				//username, newusername, showname, kshow_id, entry_id, 
				$bodyParamsArray = array ( $this->kuser->getScreenName(), $this->additionalParamsArray['screenname'],$this->additionalParamsArray['kshow_name'], $this->additionalParamsArray['kshow_id'], $this->additionalParamsArray['entry_id'] );
				break;
			case alert::KALTURAS_PRODUCED_ALERT_TYPE_SUBSCRIBER_ADDED: 
				$bodyParamsArray = array ( $this->kuser->getScreenName(), $this->additionalParamsArray['screenname'],$this->additionalParamsArray['kshow_name'], $this->additionalParamsArray['screenname'], $this->additionalParamsArray['screenname'] );
				break;
			case alert::KALTURAS_PRODUCED_ALERT_TYPE_ROUGHCUT_CREATED: 
				$bodyParamsArray = array ( $this->kuser->getScreenName(), $this->additionalParamsArray['screenname'],$this->additionalParamsArray['kshow_name'], $this->additionalParamsArray['kshow_id'], $this->additionalParamsArray['screenname'], $this->additionalParamsArray['screenname'] );
				break;
			case alert::KALTURAS_PRODUCED_ALERT_TYPE_FAVORITED: 
				$bodyParamsArray = array ( $this->kuser->getScreenName(), $this->additionalParamsArray['screenname'],$this->additionalParamsArray['kshow_name'], $this->additionalParamsArray['kshow_id'], $this->additionalParamsArray['screenname'], $this->additionalParamsArray['screenname'] );
				break;
			case alert::KALTURAS_PRODUCED_ALERT_TYPE_COMMENT_ADDED: 
				$bodyParamsArray = array ( $this->kuser->getScreenName(), $this->additionalParamsArray['screenname'],$this->additionalParamsArray['kshow_name'], $this->additionalParamsArray['kshow_id'], $this->additionalParamsArray['screenname'], $this->additionalParamsArray['screenname'] );
				break;
			
			case alert::KALTURAS_PARTOF_ALERT_TYPE_CONTRIB_ADDED: 
				//username, newusername, showname, kshow_id, entry_id, 
				$bodyParamsArray = array ( $this->kuser->getScreenName(), $this->additionalParamsArray['screenname'],$this->additionalParamsArray['kshow_name'], $this->additionalParamsArray['kshow_id'], $this->additionalParamsArray['entry_id'] );
				break;
			case alert::KALTURAS_PARTOF_ALERT_TYPE_ROUGHCUT_CREATED: 
				$bodyParamsArray = array ( $this->kuser->getScreenName(), $this->additionalParamsArray['screenname'],$this->additionalParamsArray['kshow_name'], $this->additionalParamsArray['kshow_id'], $this->additionalParamsArray['screenname'], $this->additionalParamsArray['screenname'] );
				break;
			case alert::KALTURAS_PARTOF_ALERT_TYPE_FAVORITED: 
				$bodyParamsArray = array ( $this->kuser->getScreenName(), $this->additionalParamsArray['screenname'],$this->additionalParamsArray['kshow_name'], $this->additionalParamsArray['kshow_id'], $this->additionalParamsArray['screenname'], $this->additionalParamsArray['screenname'] );
				break;
			case alert::KALTURAS_PARTOF_ALERT_TYPE_COMMENT_ADDED: 
				$bodyParamsArray = array ( $this->kuser->getScreenName(), $this->additionalParamsArray['screenname'],$this->additionalParamsArray['kshow_name'], $this->additionalParamsArray['kshow_id'], $this->additionalParamsArray['screenname'], $this->additionalParamsArray['screenname'] );
				break;
			
			case alert::KALTURAS_SUBSCRIBEDTO_ALERT_TYPE_CONTRIB_ADDED: 
				//username, newusername, showname, kshow_id, entry_id, 
				$bodyParamsArray = array ( $this->kuser->getScreenName(), $this->additionalParamsArray['screenname'],$this->additionalParamsArray['kshow_name'], $this->additionalParamsArray['kshow_id'], $this->additionalParamsArray['entry_id'] );
				break;
			case alert::KALTURAS_SUBSCRIBEDTO_ALERT_TYPE_ROUGHCUT_CREATED: 
				$bodyParamsArray = array ( $this->kuser->getScreenName(), $this->additionalParamsArray['screenname'],$this->additionalParamsArray['kshow_name'], $this->additionalParamsArray['kshow_id'], $this->additionalParamsArray['screenname'], $this->additionalParamsArray['screenname'] );
				break;
			case alert::KALTURAS_SUBSCRIBEDTO_ALERT_TYPE_FAVORITED: 
				$bodyParamsArray = array ( $this->kuser->getScreenName(), $this->additionalParamsArray['screenname'],$this->additionalParamsArray['kshow_name'], $this->additionalParamsArray['kshow_id'], $this->additionalParamsArray['screenname'], $this->additionalParamsArray['screenname'] );
				break;
			case alert::KALTURAS_SUBSCRIBEDTO_ALERT_TYPE_COMMENT_ADDED: 
				$bodyParamsArray = array ( $this->kuser->getScreenName(), $this->additionalParamsArray['screenname'],$this->additionalParamsArray['kshow_name'], $this->additionalParamsArray['kshow_id'], $this->additionalParamsArray['screenname'], $this->additionalParamsArray['screenname'] );
				break;
				
		}
		
		return $bodyParamsArray;
		
	}
	
	
}
