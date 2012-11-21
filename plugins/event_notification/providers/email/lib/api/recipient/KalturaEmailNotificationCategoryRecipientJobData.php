<?php
/**
 * Job Data representing the provider of recipients for a single categoryId
 *
 * @package plugins.emailNotification
 * @subpackage model.data
 */
class KalturaEmailNotificationCategoryRecipientJobData extends KalturaEmailNotificationRecipientJobData
{
	/**
	 * The ID of the category whose subscribers should receive the email notification.
	 * @var int
	 */
	public $categoryId;
	
	private static $map_between_objects = array(
		'categoryId',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
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
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kEmailNotificationCategoryRecipientJobData();
		
		return parent::toObject($dbObject, $propertiesToSkip);
	}
}