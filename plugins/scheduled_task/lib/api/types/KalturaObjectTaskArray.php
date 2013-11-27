<?php

/**
 * @package plugins.scheduledTask
 * @subpackage api.objects
 */
class KalturaObjectTaskArray extends KalturaTypedArray
{
	public function __construct()
	{
		parent::__construct('KalturaObjectTask');
	}

	public static function fromDbArray($dbArray)
	{
		$apiArray = new KalturaObjectTaskArray();
		foreach($dbArray as $dbObject)
		{
			/** @var kObjectTask $dbObject */
			$apiObject = KalturaObjectTask::getInstanceByDbObject($dbObject);
			$apiObject->fromObject($dbObject);
			$apiArray[] = $apiObject;
		}

		return $apiArray;
	}
}