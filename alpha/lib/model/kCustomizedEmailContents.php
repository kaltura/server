<?php
/**
 * @package Core
 * @subpackage model
 */
class kCustomizedEmailContents
{
	/**
	 * @var string
	 */
	public $emailSubject;
	
	/**
	 * @var string
	 */
	public $emailBody;
	
	/**
	 * @var string
	 */
	public $baseLink;
	
	public function setEmailSubject($emailSubject)
	{
		$this->emailSubject = $emailSubject;
	}
	
	public function setEmailBody($emailBody)
	{
		$this->emailBody = $emailBody;
	}
	
	public function setBaseLink($baseLink)
	{
		$this->baseLink = $baseLink;
	}
	
	public function getEmailSubject()
	{
		return $this->emailSubject;
	}
	
	public function getEmailBody()
	{
		return $this->emailBody;
	}
	
	public function getBaseLink()
	{
		return $this->baseLink;
	}
}
