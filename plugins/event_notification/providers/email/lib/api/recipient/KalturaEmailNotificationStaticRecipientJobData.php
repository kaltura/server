<?php
/**
 * JobData representing the static receipient array
 *
 * @package plugins.eventNotification
 * @subpackage model.data
 */
class KalturaEmailNotificationStaticRecipientJobData extends KalturaEmailNotificationRecipientJobData
{
	/**
	 * Email to emails and names
	 * @var KalturaKeyValueArray
	 */
	public $emailRecipients;
	
	/* (non-PHPdoc)
	 * @see KalturaEmailNotificationRecipientJobData::setProviderType()
	 */
	protected function setProviderType() 
	{
		$this->providerType = KalturaEmailNotificationRecipientProviderType::STATIC_LIST;	
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject($source_object)
	 */
	public function fromObject($dbObject)
	{
		/* @var $dbObject kEmailNotificationStaticRecipientJobData */
		parent::fromObject($dbObject);
		$this->setProviderType();
		
		$this->emailRecipients = KalturaKeyValueArray::fromKeyValueArray($dbObject->getEmailRecipients());
	}

	
}