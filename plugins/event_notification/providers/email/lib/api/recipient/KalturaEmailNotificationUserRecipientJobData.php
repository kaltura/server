<?php
/**
 * JobData representing the dynamic user receipient array
 *
 * @package plugins.emailNotification
 * @subpackage model.data
 */
class KalturaEmailNotificationUserRecipientJobData extends KalturaEmailNotificationRecipientJobData
{
	/**
	 * @var KalturaUserFilter
	 */
	public $filter;
	
	private static $map_between_objects = array(
		'filter',
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
		$this->providerType = KalturaEmailNotificationRecipientProviderType::USER;	
		
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject($source_object)
	 */
	public function fromObject($dbObject, KalturaResponseProfileBase $responseProfile = null)
	{
		/* @var $dbObject kEmailNotificationStaticRecipientJobData */
		parent::fromObject($dbObject, $responseProfile);
		$this->setProviderType();
		if ($dbObject->getFilter())
		{
			$this->filter = new KalturaUserFilter();
			$this->filter->fromObject($dbObject->getFilter());
		}
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kEmailNotificationUserRecipientJobData();
		
		return parent::toObject($dbObject, $propertiesToSkip);
	}
}