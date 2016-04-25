<?php

/**
 * @package plugins.scheduleDropFolder
 * @subpackage model.data
 */
class DropFolderICalBulkUploadFileHandlerConfig extends DropFolderFileHandlerConfig
{
	/**
	 * @var ScheduleEventType
	 */
	private $eventsType;
	

	public function getHandlerType() {
		return kPluginableEnumsManager::coreToApi("KalturaBulkUploadType", BulkUploadSchedulePlugin::getApiValue(BulkUploadScheduleType::ICAL));
	}
	
	/**
	 * @return the $eventsType
	 */
	public function getEventsType()
	{
		return $this->eventsType;
	}

	/**
	 * @param ScheduleEventType $eventsType
	 */
	public function setEventsType($eventsType)
	{
		$this->eventsType = $eventsType;
	}
	
}