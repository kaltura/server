<?php
/**
 * JobData representing the dynamic user receipient array
 *
 * @package plugins.emailNotification
 * @subpackage model.data
 */
class KalturaEmailNotificationGroupRecipientJobData extends KalturaEmailNotificationRecipientJobData
{
	/**
	 * @var string
	 */
	public $groupId;
	
	private static $map_between_objects = array(
		'groupId',
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
	protected function setProviderType() {
		$this->providerType = KalturaEmailNotificationRecipientProviderType::GROUP;	
		
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject($source_object)
	 */
	public function doFromObject($dbObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var $dbObject kEmailNotificationStaticRecipientJobData */
		parent::doFromObject($dbObject, $responseProfile);
		$this->setProviderType();
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kEmailNotificationGroupRecipientJobData();
		
		return parent::toObject($dbObject, $propertiesToSkip);
	}
}