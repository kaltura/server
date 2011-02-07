<?php

/**
 * Subclass for representing a row from the 'mail_job' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class MailJob extends BaseMailJob
{
	const RECIPIENT_SEPARATOR = ",";
	
	
	/**
	 * will verify if each recipient should be getting this email
	 */
	public function save(PropelPDO $con = null)
	{
		// before saving - remove all the recipients that are in the blocked_email list
		$recipient_list = explode ( self::RECIPIENT_SEPARATOR , $this->recipient_email );
		$new_recipient_email = ""; 
		foreach ( $recipient_list as $recipient )
		{
			if ( $new_recipient_email != "" ) $new_recipient_email .= self::RECIPIENT_SEPARATOR;
			if ( myBlockedEmailUtils::shouldSendEmail( trim ( $recipient ) ) )
			{
				$new_recipient_email .=  $recipient;
			}
		}
		$this->recipient_email = $new_recipient_email;
		if ( $this->recipient_email != "" )
		{
			if ( $this->isNew() )
			{
				$this->setDc ( kDataCenterMgr::getCurrentDcId());
			}
			parent::save( $con );
		}
	}
	
	public function Initialize( $type, $priority = MailJobPeer::MAIL_PRIORITY_NORMAL )
	{
		$this->setMailType( $type );
		$this->setMailPriority( $priority );
		$this->setStatus(  MailJobPeer::MAIL_STATUS_PENDING );
		$this->setCreatedAt( time() );	
	}

	public function setBodyParamsArray( $paramsArray )
	{
		$paramsstring = '';
		if ( is_array( $paramsArray ) ) foreach( $paramsArray as $param )
		{
			$paramsstring =  ( $paramsstring ? $paramsstring.'|' : '' ).$param; 
		}
		$this->setBodyParams( $paramsstring );
	}

	public function getBodyParamsArray()
	{
		return explode ( "|", $this->getBodyParams() );
	}
	
	public function setSubjectParamsArray( $paramsArray )
	{
		$paramsstring = '';
		if ( is_array( $paramsArray ) ) foreach( $paramsArray as $param )
		{
			$paramsstring =  ( $paramsstring ? $paramsstring.'|' : '' ).$param; 
		}
		$this->setSubjectParams( $paramsstring );
	}
	
	public function getSubjectParamsArray()
	{
		return explode ( "|", $this->getSubjectParams() );
	}

	
	public function isRetriesExceeded()
	{
		return ($this->execution_attempts >= self::MAX_EXECUTION_ATTEMPTS);
	}	
}
