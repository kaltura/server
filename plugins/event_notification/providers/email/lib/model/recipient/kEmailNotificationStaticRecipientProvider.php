<?php
/**
 * Core class for recipient provider containing a static list of email recipients.
 *
 * @package plugins.eventNotification
 * @subpackage model.data
 */
class kEmailNotificationStaticRecipientsProvider extends kEmailNotificationRecipientProvider
{
	/**
	 * Email notification "to" sendees
	 * @var array
	 */
	protected $emailRecipients;

	/**
	 * @return the $to
	 */
	public function getEmailRecipients() {
		return $this->emailRecipients;
	}

	/**
	 * @param field_type $to
	 */
	public function setEmailRecipients($v) {
		$this->emailRecipients = $v;
	}
	
	/* (non-PHPdoc)
	 * @see kEmailNotificationRecipientProvider::applyScope()
	 */
	public function applyScope(kScope $scope) 
	{
		$implicitEmailRecipients = array();
		foreach($this->emailRecipients as &$emailRecipient)
		{
			/* @var $emailRecipient kEmailNotificationRecipient */
			$email = $emailRecipient->getEmail();
			if($scope && $email instanceof kStringField)
				$email->setScope($scope);

			$name = $emailRecipient->getName();
			if($scope && $name instanceof kStringField)
				$name->setScope($scope);
			$theName = "";
            if ($name)
			    $theName = $name->getValue();
			    			
			$implicitEmailRecipients[$email->getValue()] = $theName;
		}
		
		$ret = new kEmailNotificationStaticRecipientJobData();
		$ret->setEmailRecipients($implicitEmailRecipients);
		
		return $ret;
		
	}
}