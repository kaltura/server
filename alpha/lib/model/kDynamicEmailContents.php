<?php
/**
 * @package Core
 * @subpackage model
 */
class kDynamicEmailContents
{
	/**
	 * @var string
	 */
	public $emailSubject;
	
	/**
	 * @var string
	 */
	public $emailBody;
	
	public function setEmailSubject($emailSubject)
	{
		$this->emailSubject = $emailSubject;
	}
	
	public function setEmailBody($emailBody)
	{
		$this->emailBody = $emailBody;
	}
	
	public function getEmailSubject()
	{
		return $this->emailSubject;
	}
	
	public function getEmailBody()
	{
		return $this->emailBody;
	}
}