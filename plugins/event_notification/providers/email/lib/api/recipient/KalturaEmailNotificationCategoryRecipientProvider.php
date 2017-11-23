<?php
/**
 * API object which provides the recipients of category related notifications.
 *
 * @package plugins.emailNotification
 * @subpackage model.data
 */
class KalturaEmailNotificationCategoryRecipientProvider extends KalturaEmailNotificationRecipientProvider
{
	/**
	 * The ID of the category whose subscribers should receive the email notification.
	 * @var KalturaStringValue
	 */
	public $categoryId;

	/**
	 * The IDs of the categories whose subscribers should receive the email notification.
	 * @var KalturaStringValue
	 */
	public $categoryIds;
	
	/**
	 *
	 * @var KalturaCategoryUserProviderFilter
	 */
	public $categoryUserFilter;

	private static $map_between_objects = array(
		'categoryId',
		'categoryIds',
		'categoryUserFilter',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		$this->validate();
		if (is_null($dbObject))
			$dbObject = new kEmailNotificationCategoryRecipientProvider();
			
		return parent::toObject($dbObject, $propertiesToSkip);
	}	
	
	/**
	 * Validation function
	 * @throws KalturaEmailNotificationErrors::INVALID_FILTER_PROPERTY
	 */
	protected function validate ()
	{
		if ($this->categoryUserFilter)
		{
			if (isset ($this->categoryUserFilter->categoryIdEqual))
			{
				throw new KalturaAPIException(KalturaEmailNotificationErrors::INVALID_FILTER_PROPERTY, 'categoryIdEqual');
			}
		}
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject($source_object)
	 */
	public function doFromObject($dbObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($dbObject, $responseProfile);
		/* @var $dbObject kEmailNotificationCategoryRecipientProvider */
		$categoryIdFieldType = get_class($dbObject->getCategoryId());
		KalturaLog::info("Retrieving API object for categoryId field of type [$categoryIdFieldType]");
		switch ($categoryIdFieldType)
		{
			case 'kObjectIdField':
				$this->categoryId = new KalturaObjectIdField();
				break;
			case 'kEvalStringField':
				$this->categoryId = new KalturaEvalStringField();
				break;
			case 'kStringValue':
				$this->categoryId = new KalturaStringValue();
				break;
			default:
				$this->categoryId = KalturaPluginManager::loadObject('KalturaStringValue', $categoryIdFieldType);
				break;
		}
		
		if ($this->categoryId)
		{
			$this->categoryId->fromObject($dbObject->getCategoryId());
		}

		$categoryIdsFieldType = get_class($dbObject->getCategoryIds());
		KalturaLog::info("Retrieving API object for categoryIds field of type [$categoryIdsFieldType]");
		switch ($categoryIdsFieldType)
		{
			case 'kEvalStringField':
				$this->categoryIds = new KalturaEvalStringField();
				break;
			case 'kStringValue':
				$this->categoryIds = new KalturaStringValue();
				break;
			default:
				$this->categoryIds = KalturaPluginManager::loadObject('KalturaStringValue', $categoryIdFieldType);
				break;
		}

		if ($this->categoryIds)
		{
			$this->categoryIds->fromObject($dbObject->getCategoryIds());
		}

		if ($dbObject->getCategoryUserFilter())
		{
			$this->categoryUserFilter = new KalturaCategoryUserProviderFilter();
			$this->categoryUserFilter->fromObject($dbObject->getCategoryUserFilter());
		}

	}
} 