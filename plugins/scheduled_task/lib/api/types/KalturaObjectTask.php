<?php

/**
 * @package plugins.scheduledTask
 * @subpackage api.objects
 */
abstract class KalturaObjectTask extends KalturaObject
{
	/**
	 * @readonly
	 * @var KalturaObjectTaskType
	 */
	public $type;

	/**
	 * @var bool
	 */
	public $stopProcessingOnError;

	/*
	 */
	private static $map_between_objects = array(
		'type',
		'stopProcessingOnError',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kObjectTask();

		return parent::toObject($dbObject, $skip);
	}

	/**
	 * @param array $propertiesToSkip
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('stopProcessingOnError');
	}

	static function getInstanceByDbObject(kObjectTask $dbObject)
	{
		switch($dbObject->getType())
		{
			case ObjectTaskType::DELETE_ENTRY:
				return new KalturaDeleteEntryObjectTask();
			case ObjectTaskType::MODIFY_CATEGORIES:
				return new KalturaModifyCategoriesObjectTask();
			case ObjectTaskType::DELETE_ENTRY_FLAVORS:
				return new KalturaDeleteEntryFlavorsObjectTask();
			case ObjectTaskType::CONVERT_ENTRY_FLAVORS:
				return new KalturaConvertEntryFlavorsObjectTask();
			case ObjectTaskType::DELETE_LOCAL_CONTENT:
				return new KalturaDeleteLocalContentObjectTask();
			case ObjectTaskType::STORAGE_EXPORT:
				return new KalturaStorageExportObjectTask();
			case ObjectTaskType::MODIFY_ENTRY:
				return new KalturaModifyEntryObjectTask();
			case ObjectTaskType::MAIL_NOTIFICATION:
				return new KalturaMailNotificationObjectTask();
			default:
				return KalturaPluginManager::loadObject('KalturaObjectTask', $dbObject->getType());
		}
	}
}
