<?php
/**
 * @package plugins.emailNotification
 * @subpackage model.data
 */
class kEmailNotificationDispatchJobData extends kEventNotificationDispatchJobData
{
	/**
	 * Define the email sender email
	 * @var string
	 */
	private $fromEmail;
	
	/**
	 * Define the email sender name
	 * @var string
	 */
	private $fromName;
	
	/**
	 * Define the email receipient email
	 * @var string
	 */
	private $toEmail;
	
	/**
	 * Define the email receipient name
	 * @var string
	 */
	private $toName;
	
	/**
	 * @return the $fromEmail
	 */
	public function getFromEmail() 
	{
		return $this->fromEmail;
	}

	/**
	 * @return the $fromName
	 */
	public function getFromName()  
	{
		return $this->fromName;
	}

	/**
	 * @return the $toEmail
	 */
	public function getToEmail()  
	{
		return $this->toEmail;
	}

	/**
	 * @return the $toName
	 */
	public function getToName()  
	{
		return $this->toName;
	}

	/**
	 * @param string $fromEmail
	 */
	public function setFromEmail($fromEmail)  
	{
		$this->fromEmail = $fromEmail;
	}

	/**
	 * @param string $fromName
	 */
	public function setFromName($fromName)  
	{
		$this->fromName = $fromName;
	}

	/**
	 * @param string $toEmail
	 */
	public function setToEmail($toEmail)  
	{
		$this->toEmail = $toEmail;
	}

	/**
	 * @param string $toName
	 */
	public function setToName($toName)  
	{
		$this->toName = $toName;
	}
}