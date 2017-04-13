<?php

/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */

class MediaRepurposingUtils
{

	const MEDIA_REPURPOSING_SYSTEM_NAME = 'MRP';
	const MAX_TOTAL_COUNT_ALLOWED = 500;

	/**
	 * get all Media Repurposing of the partner
	 * @param int $partnerId
	 * @return array of KalturaScheduledTaskProfile
	 */
	public static function getMrs($partnerId) {
		if (!$partnerId)
			return array();
		$scheduledtaskPlugin = $scheduledtaskPlugin = self::getPluginByName('Kaltura_Client_ScheduledTask_Plugin');
		$filter = new Kaltura_Client_ScheduledTask_Type_ScheduledTaskProfileFilter();
		$filter->partnerIdEqual = 104;
		$filter->systemNameEqual = self::MEDIA_REPURPOSING_SYSTEM_NAME;
		$result = $scheduledtaskPlugin->scheduledTaskProfile->listAction($filter, null);

		return $result->objects;

	}

	public static function getPluginByName($name, $partnerId = null) {
		$client = Infra_ClientHelper::getClient();
		if ($partnerId)
			$client->setPartnerId($partnerId);
		$plugin = $name::get($client);
		return $plugin;
	}



	public static function changeMrStatus($mr, $newStatus) {
		$scheduledtaskPlugin = self::getPluginByName('Kaltura_Client_ScheduledTask_Plugin');

		$scheduledTaskProfile = new Kaltura_Client_ScheduledTask_Type_ScheduledTaskProfile();
		$scheduledTaskProfile->status = $newStatus;

		$scheduleTaskIds = explode(',', $mr->description);
		KalturaLog::info("starting changing status of media repurpesing and its ST [$mr->description] to $newStatus");
		foreach ($scheduleTaskIds as $scheduleTaskId)
			if ($scheduleTaskId)
				$result = $scheduledtaskPlugin->scheduledTaskProfile->update($scheduleTaskId, $scheduledTaskProfile);

		$mr->status = $newStatus;
		$result = $scheduledtaskPlugin->scheduledTaskProfile->update($mr->id, $scheduledTaskProfile);
	}

	
	public static function createNewMr($name, $filterTypeEngine, $filter, $taskArray, $partnerId) {
		$scheduledTaskPlugin = self::getPluginByName('Kaltura_Client_ScheduledTask_Plugin', $partnerId);

		$mr = self::createST($name, $filterTypeEngine, $filter, $taskArray[0]);
		$mr->systemName = self::MEDIA_REPURPOSING_SYSTEM_NAME;
		$result = $scheduledTaskPlugin->scheduledTaskProfile->add($mr);

		$mrId = $result->id;
/*
		$ids = '';
		for ($i = 2; $i < count($taskArray); $i += 2) {
			$timeAfterLast = $taskArray[$i-1];

			//$filter->LastViewed += $timeToNext;
			//$filter->MR[$mrId] == ($i/2)
			//TODO add on the entryEngine: $filter->status on entry is not disable
			$scheduleTaskName = self::getSubScheduleTaskName($name, $i);
			$result = self::addScheduleTask($scheduledTaskPlugin, $scheduleTaskName, $filterTypeEngine, $filter, $taskArray[$i]);

			KalturaLog::info("task index: " .$i );
			KalturaLog::info("wait: " . $timeAfterLast);
			KalturaLog::info(print_r($result->id,true));


			if (strlen($ids))
				$ids.= ",";
			$ids.= $result->id . "[$timeAfterLast]";
		}

		$mr = new Kaltura_Client_ScheduledTask_Type_ScheduledTaskProfile();
		$mr->description = $ids;
*/

		$mr = new Kaltura_Client_ScheduledTask_Type_ScheduledTaskProfile();
		$mr->description = self::handleSts($scheduledTaskPlugin, $name, $filterTypeEngine, $filter, $taskArray);
		return $scheduledTaskPlugin->scheduledTaskProfile->update($mrId, $mr);
	}



	public static function UpdateMr($id, $name, $filterTypeEngine, $filter, $taskArray, $partnerId) {
		$scheduledtaskPlugin = self::getPluginByName('Kaltura_Client_ScheduledTask_Plugin', $partnerId);

		$taskArray[0]->relatedObjects = null;
		$mr = self::createST($name, $filterTypeEngine, $filter, $taskArray[0]);
		$mr->systemName = self::MEDIA_REPURPOSING_SYSTEM_NAME;
/*
		//KalturaLog::info("qwer");
		//KalturaLog::info(print_r($taskArray, true));
		//return;


		$ids = '';
		for ($i = 2; $i < count($taskArray); $i += 2) {
			$sdId = $taskArray[$i]->relatedObjects;
			$taskArray[$i]->relatedObjects = null;
			$timeAfterLast = $taskArray[$i-1];
			//TODO change filter according to $timeAfterLast
			$stName = self::getSubScheduleTaskName($name, $i);
			$scheduledTaskProfile = self::createST($stName, $filterTypeEngine, $filter, $taskArray[$i]);

			KalturaLog::info("Updating Schedule Task [$sdId] who should run [$timeAfterLast] days after last ST:");
			KalturaLog::info(print_r($scheduledTaskProfile, true));


			//add task to API
			if ($sdId)
				$result = $scheduledtaskPlugin->scheduledTaskProfile->update($sdId, $scheduledTaskProfile);
			else
				$result = $scheduledtaskPlugin->scheduledTaskProfile->add($scheduledTaskProfile);

			if (strlen($ids))
				$ids.= ",";
			$ids.= $result->id . "[$timeAfterLast]";
		}

		$mr->description = $ids;
*/
		$mr->description = self::handleSts($scheduledtaskPlugin, $name, $filterTypeEngine, $filter, $taskArray);
		return $scheduledtaskPlugin->scheduledTaskProfile->update($id, $mr);

	}

	private static function handleSts($scheduledtaskPlugin, $name, $filterTypeEngine, $filter, $taskArray)
	{
		$ids = '';
		for ($i = 2; $i < count($taskArray); $i += 2) {
			$sdId = $taskArray[$i]->relatedObjects;
			$taskArray[$i]->relatedObjects = null;

			$timeAfterLast = $taskArray[$i-1];
			//TODO change filter according to $timeAfterLast
			//$filter->LastViewed += $timeAfterLast;
			//$filter->MR[$mrId] == ($i/2)
			//TODO add on the entryEngine: $filter->status on entry is not disable

			$stName = self::getSubScheduleTaskName($name, $i);

			$scheduledTaskProfile = self::createST($stName, $filterTypeEngine, $filter, $taskArray[$i]);

			KalturaLog::info("Handle Schedule Task [$sdId] who should run [$timeAfterLast] days after last ST:");
			KalturaLog::info(print_r($scheduledTaskProfile, true));

			//add task to API
			if ($sdId)
				$result = $scheduledtaskPlugin->scheduledTaskProfile->update($sdId, $scheduledTaskProfile);
			else
				$result = $scheduledtaskPlugin->scheduledTaskProfile->add($scheduledTaskProfile);

			if (strlen($ids))
				$ids.= ",";
			$ids.= $result->id . "[$timeAfterLast]";
		}
		return $ids;
	}

	private static function addScheduleTask($scheduledtaskPlugin, $name, $filterTypeEngine, $filter, $task)
	{
		$scheduledTaskProfile = self::createST($name, $filterTypeEngine, $filter, $task);
		return $scheduledtaskPlugin->scheduledTaskProfile->add($scheduledTaskProfile);
	}

	private static function createST($name, $filterTypeEngine, $filter, $task)
	{
		$st = new Kaltura_Client_ScheduledTask_Type_ScheduledTaskProfile();
		$st->name = $name;
		$st->status = ScheduledTaskProfileStatus::DISABLED;
		$st->objectFilter = $filter;
		$st->objectFilterEngineType = $filterTypeEngine;
		$st->maxTotalCountAllowed = self::MAX_TOTAL_COUNT_ALLOWED;
		$st->objectTasks = array($task);
		return $st;
	}



	public static function objectTaskFactory($type) {
		switch($type)
		{
			case Kaltura_Client_ScheduledTask_Enum_ObjectTaskType::DELETE_ENTRY_FLAVORS:
				return new Kaltura_Client_ScheduledTask_Type_DeleteEntryFlavorsObjectTask();

			case Kaltura_Client_ScheduledTask_Enum_ObjectTaskType::CONVERT_ENTRY_FLAVORS:
				return new Kaltura_Client_ScheduledTask_Type_ConvertEntryFlavorsObjectTask();

			case Kaltura_Client_ScheduledTask_Enum_ObjectTaskType::DELETE_LOCAL_CONTENT:
				return new Kaltura_Client_ScheduledTask_Type_DeleteLocalContentObjectTask();

			case Kaltura_Client_ScheduledTask_Enum_ObjectTaskType::STORAGE_EXPORT:
				return new Kaltura_Client_ScheduledTask_Type_StorageExportObjectTask();

			case Kaltura_Client_ScheduledTask_Enum_ObjectTaskType::MODIFY_CATEGORIES:
				return new Kaltura_Client_ScheduledTask_Type_ModifyCategoriesObjectTask();

			case Kaltura_Client_ScheduledTask_Enum_ObjectTaskType::MODIFY_ENTRY:
				return new Kaltura_Client_ScheduledTask_Type_ModifyEntryObjectTask();

			case Kaltura_Client_ScheduledTask_Enum_ObjectTaskType::DELETE_ENTRY:
				return new Kaltura_Client_ScheduledTask_Type_DeleteEntryObjectTask();

			case Kaltura_Client_ScheduledTask_Enum_ObjectTaskType::EXECUTE_METADATA_XSLT:
				return new Kaltura_Client_ScheduledTaskMetadata_Type_ExecuteMetadataXsltObjectTask();

			case Kaltura_Client_ScheduledTask_Enum_ObjectTaskType::DISPATCH_EVENT_NOTIFICATION:
				return new Kaltura_Client_ScheduledTaskEventNotification_Type_DispatchEventNotificationObjectTask();

			case Kaltura_Client_ScheduledTask_Enum_ObjectTaskType::DISTRIBUTE:
				return new Kaltura_Client_ScheduledTaskContentDistribution_Type_DistributeObjectTask();

			default:
				return null;
		}
	}

	public static function filterFactory($type) {
		switch($type)
		{
			case Kaltura_Client_ScheduledTask_Enum_ObjectFilterEngineType::ENTRY:
				return 'Kaltura_Client_Type_BaseEntryFilter';

			default:
				return null;
		}
	}

	public static function getSubScheduleTaskName($name, $index)
	{
		return 'MR_' .$name .'_'. ($index/2);
	}

	public static $typeDescription = array("1" => 'DELETE_ENTRY',
		"2" => 'MODIFY_CATEGORIES',
		"3" => 'DELETE_ENTRY_FLAVORS',
		"4" => 'CONVERT_ENTRY_FLAVORS',
		"5" => 'DELETE_LOCAL_CONTENT',
		"6" => 'STORAGE_EXPORT',
		"7" => 'MODIFY_ENTRY',
		"scheduledTaskContentDistribution.Distribute" => 'DISTRIBUTE',
		"scheduledTaskEventNotification.DispatchEventNotification" => 'DISPATCH_EVENT_NOTIFICATION',
		"scheduledTaskMetadata.ExecuteMetadataXslt" => 'EXECUTE_METADATA_XSLT');
	//as from Kaltura_Client_ScheduledTask_Enum_ObjectTaskType
	public static function getDescriptionForType($type) {
		return self::$typeDescription[$type];
	}

	public static function addParamToObjectTask($objectTask, $params) {
		foreach ($params as $key => $value) {
			$objectTask->$key = $value;
		}
	}

}