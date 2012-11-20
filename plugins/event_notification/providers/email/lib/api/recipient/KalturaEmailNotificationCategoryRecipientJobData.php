<?php
/**
 * Job Data representing the provider of recipients for a single categoryId
 *
 * @package plugins.eventNotification
 * @subpackage model.data
 */
class KalturaEmailNotificationCategoryRecipientJobData extends KalturaEmailNotificationRecipientJobData
{
	/**
	 * The ID of the category whose subscribers should receive the email notification.
	 * @var int
	 */
	public $categoryId;
	
	/* (non-PHPdoc)
	 * @see KalturaEmailNotificationRecipientJobData::setProviderType()
	 */
	protected function setProviderType() 
	{
		$this->providerType = KalturaEmailNotificationRecipientProviderType::CATEGORY;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject($source_object)
	 */
	public function fromObject($source_object)
	{
		parent::fromObject($source_object);
		$this->setProviderType();
	}
}