<?php

/** 
 * @package Core
 * @subpackage Batch
 */
class kMailJobData
{
	const MAIL_STATUS_PENDING = 1;
	const MAIL_STATUS_SENT = 2;
	const MAIL_STATUS_ERROR = 3;
	const MAIL_STATUS_QUEUED = 4;
	
	const MAIL_PRIORITY_REALTIME = 1;
	const MAIL_PRIORITY_HIGH = 2;
	const MAIL_PRIORITY_NORMAL = 2;
	const MAIL_PRIORITY_LOW = 3;
	
	
	/**
	 * @var KalturaMailType
	 */
	private $mailType;

	/**
	 * @var int
	 */
    private $mailPriority;

    /**
	 * @var KalturaMailJobStatus
	 */
    private $status ;
    
	/**
	 * @var string
	 */
	private $recipientName;  

	/**
	 * @var string
	 */	
   	private $recipientEmail;
   	
	/**
	 * kuserId  
	 * @var int
	 */   	
    private $recipientId;
    
	/**
	 * @var string
	 */    
    private $fromName;
    
	/**
	 * @var string
	 */    
    private $fromEmail;
  
	/**
	 * @var string
	 */    
    private $bodyParams;

	/**
	 * @var string
	 */    
    private $subjectParams;  

	/**
 	* @var string
 	*/
    private $templatePath;

	/**
 	* @var int
 	*/
    private $culture;

	/**
 	* @var int
 	*/
    private $campaignId;

	/**
 	* @var int
 	*/
    private $minSendDate;
    
    /**
 	* @var bool
 	*/
    private $isHtml;
    
	/**
	 * @return the $mailType
	 */
	public function getMailType()
	{
		return $this->mailType;
	}

	/**
	 * @return the $mailPriority
	 */
	public function getMailPriority()
	{
		return $this->mailPriority;
	}

	/**
	 * @return the $status
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @return the $recipientName
	 */
	public function getRecipientName()
	{
		return $this->recipientName;
	}

	/**
	 * @return the $recipientEmail
	 */
	public function getRecipientEmail()
	{
		return $this->recipientEmail;
	}

	/**
	 * @return the $recipientId
	 */
	public function getRecipientId()
	{
		return $this->recipientId;
	}

	/**
	 * @return the $fromName
	 */
	public function getFromName()
	{
		return $this->fromName;
	}

	/**
	 * @return the $fromEmail
	 */
	public function getFromEmail()
	{
		return $this->fromEmail;
	}

	/**
	 * @return the $bodyParams
	 */
	public function getBodyParams()
	{
		return $this->bodyParams;
	}

	/**
	 * @return the $subjectParams
	 */
	public function getSubjectParams()
	{
		return $this->subjectParams;
	}

	/**
	 * @return the $templatePath
	 */
	public function getTemplatePath()
	{
		return $this->templatePath;
	}

	/**
	 * @return the $culture
	 */
	public function getCulture()
	{
		return $this->culture;
	}

	/**
	 * @return the $campaignId
	 */
	public function getCampaignId()
	{
		return $this->campaignId;
	}

	/**
	 * @return the $minSendDate
	 */
	public function getMinSendDate()
	{
		return $this->minSendDate;
	}
	
	/**
	 * @return the $isHtml
	 */
	public function getIsHtml()
	{
		return $this->isHtml;
	}

	/**
	 * @param $mailType the $mailType to set
	 */
	public function setMailType($mailType)
	{
		$this->mailType = $mailType;
	}

	/**
	 * @param $mailPriority the $mailPriority to set
	 */
	public function setMailPriority($mailPriority)
	{
		$this->mailPriority = $mailPriority;
	}

	/**
	 * @param $status the $status to set
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}

	/**
	 * @param $recipientName the $recipientName to set
	 */
	public function setRecipientName($recipientName)
	{
		$this->recipientName = $recipientName;
	}

	/**
	 * @param $recipientEmail the $recipientEmail to set
	 */
	public function setRecipientEmail($recipientEmail)
	{
		$this->recipientEmail = $recipientEmail;
	}

	/**
	 * @param $recipientId the $recipientId to set
	 */
	public function setRecipientId($recipientId)
	{
		$this->recipientId = $recipientId;
	}

	/**
	 * @param $fromName the $fromName to set
	 */
	public function setFromName($fromName)
	{
		$this->fromName = $fromName;
	}

	/**
	 * @param $fromEmail the $fromEmail to set
	 */
	public function setFromEmail($fromEmail)
	{
		$this->fromEmail = $fromEmail;
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
	
	/**
	 * @param $bodyParams the $bodyParams to set
	 */
	public function setBodyParams($bodyParams)
	{
		$this->bodyParams = $bodyParams;
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
	
	/**
	 * @param $subjectParams the $subjectParams to set
	 */
	public function setSubjectParams($subjectParams)
	{
		$this->subjectParams = $subjectParams;
	}

	/**
	 * @param $templatePath the $templatePath to set
	 */
	public function setTemplatePath($templatePath)
	{
		$this->templatePath = $templatePath;
	}

	/**
	 * @param $culture the $culture to set
	 */
	public function setCulture($culture)
	{
		$this->culture = $culture;
	}

	/**
	 * @param $campaignId the $campaignId to set
	 */
	public function setCampaignId($campaignId)
	{
		$this->campaignId = $campaignId;
	}

	/**
	 * @param $minSendDate the $minSendDate to set
	 */
	public function setMinSendDate($minSendDate)
	{
		$this->minSendDate = $minSendDate;
	}
	
	/**
	 * @param $isHtml
	 */
	public function setIsHtml($isHtml)
	{
		$this->isHtml = $isHtml;
	}
}

?>