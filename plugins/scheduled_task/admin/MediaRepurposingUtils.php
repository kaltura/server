<?php

/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */

class MediaRepurposingUtils
{


	const STATUS_XPATH_NAME = '/*[local-name()=\'metadata\']/*[local-name()=\'Status\']';
	const MPRS_DATA_XPATH_NAME = '/*[local-name()=\'metadata\']/*[local-name()=\'MRPData\']';
	const MPRS_XPATH_NAME = '/*[local-name()=\'metadata\']/*[local-name()=\'MRPsOnEntry\']';
	const EXCLUDE = 0;

	const MEDIA_REPURPOSING_SYSTEM_NAME = 'MRP';
	/**
	 * get all Media Repurposing of the partner
	 * @param int $partnerId
	 * @return array of KalturaScheduledTaskProfile
	 */
	public static function getMrs($partnerId) {
		if (!$partnerId)
			return array();
		$scheduledtaskPlugin = self::getPluginByName('Kaltura_Client_ScheduledTask_Plugin');
		$filter = new Kaltura_Client_ScheduledTask_Type_ScheduledTaskProfileFilter();
		$filter->partnerIdEqual = $partnerId;
		$filter->systemNameEqual = self::MEDIA_REPURPOSING_SYSTEM_NAME;
		$result = $scheduledtaskPlugin->scheduledTaskProfile->listAction($filter, null);
		return $result->objects;
	}

	public static function getMrById($MrId)
	{
		$scheduledtaskPlugin = self::getPluginByName('Kaltura_Client_ScheduledTask_Plugin');
		return $scheduledtaskPlugin->scheduledTaskProfile->get($MrId);
	}

	public static function isAllowMrToPartner($partnerId)
	{
		$client = Infra_ClientHelper::getClient();
		$client->setPartnerId($partnerId);
		$filter = new Kaltura_Client_Type_PermissionFilter();
		$filter->nameEqual = Kaltura_Client_Enum_PermissionName::FEATURE_MEDIA_REPURPOSING_PERMISSION;
		$filter->partnerIdEqual = $partnerId;
		$result = $client->permission->listAction($filter, null);
		$client->setPartnerId(-2);

		return ($result->objects[0]->status == Kaltura_Client_Enum_PermissionStatus::ACTIVE);
	}


	public static function getPluginByName($name, $partnerId = null) {
		$client = Infra_ClientHelper::getClient();
		if ($partnerId)
			$client->setPartnerId($partnerId);
		else
			$client->setPartnerId(-2);
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

	
	public static function createNewMr($name, $filterTypeEngine, $filter, $taskArray, $partnerId, $maxEntriesAllowed) {
		$scheduledTaskPlugin = self::getPluginByName('Kaltura_Client_ScheduledTask_Plugin', $partnerId);

		$mr = self::createST($name, $filterTypeEngine, $filter, $taskArray[0], $maxEntriesAllowed);
		$mr->systemName = self::MEDIA_REPURPOSING_SYSTEM_NAME;
		$result = $scheduledTaskPlugin->scheduledTaskProfile->add($mr);

		$mrId = $result->id;
		$mr = new Kaltura_Client_ScheduledTask_Type_ScheduledTaskProfile();
		$mr->description = self::handleSts($mrId, $scheduledTaskPlugin, $name, $filterTypeEngine, $filter, $taskArray, $maxEntriesAllowed);
		return $scheduledTaskPlugin->scheduledTaskProfile->update($mrId, $mr);
	}

	
	public static function UpdateMr($id, $name, $filterTypeEngine, $filter, $taskArray, $partnerId, $maxEntriesAllowed) {
		$scheduledtaskPlugin = self::getPluginByName('Kaltura_Client_ScheduledTask_Plugin', $partnerId);

		$taskArray[0]->relatedObjects = null;
		$mr = self::createST($name, $filterTypeEngine, $filter, $taskArray[0], $maxEntriesAllowed);
		$mr->systemName = self::MEDIA_REPURPOSING_SYSTEM_NAME;
		$mr->objectFilter->advancedSearch->items[] = self::createMrConditionFilter($id);

		$mr->description = self::handleSts($id, $scheduledtaskPlugin, $name, $filterTypeEngine, clone($filter), $taskArray, $maxEntriesAllowed);
		return $scheduledtaskPlugin->scheduledTaskProfile->update($id, $mr);

	}

	private static function handleSts($mrId, $scheduledtaskPlugin, $name, $filterTypeEngine, $filter, $taskArray, $maxEntriesAllowed)
	{
		$ids = '';
		for ($i = 2; $i < count($taskArray); $i += 2) {
			$sdId = $taskArray[$i]->relatedObjects;
			$taskArray[$i]->relatedObjects = null;

			$timeAfterLast = $taskArray[$i-1];
			$stName = self::getSubScheduleTaskName($name, $i);

			$scheduledTaskProfile = self::createST($stName, $filterTypeEngine, $filter, $taskArray[$i], $maxEntriesAllowed);
			$scheduledTaskProfile->description = $timeAfterLast;
			$scheduledTaskProfile->objectFilter->advancedSearch->items[] = self::createMrStateConditionFilter($mrId, ($i/2));

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


	private static function createST($name, $filterTypeEngine, $filter, $task, $maxEntriesAllowed)
	{
		$st = new Kaltura_Client_ScheduledTask_Type_ScheduledTaskProfile();
		$st->name = $name;
		$st->status = ScheduledTaskProfileStatus::DISABLED;

		$filter->advancedSearch = self::createMRFilterForStatus();
		$st->objectFilter = $filter;
		$st->objectFilterEngineType = $filterTypeEngine;
		if (!$filterTypeEngine)
			$st->objectFilterEngineType = ObjectFilterEngineType::ENTRY;
		$st->maxTotalCountAllowed = $maxEntriesAllowed;
		$st->objectTasks = array($task);
		return $st;
	}


	private static function getMrMetadataProfile()
	{
		$filter = new Kaltura_Client_Metadata_Type_MetadataProfileFilter();
		$filter->systemNameEqual = 'MRP';
		$metadataPlugin = self::getPluginByName('Kaltura_Client_Metadata_Plugin');
		return $metadataPlugin->metadataProfile->listAction($filter, null);
	}


	private static function createMRFilterForStatus()
	{
		$searchItem = new Kaltura_Client_Metadata_Type_MetadataSearchItem();
		$searchItem->type = Kaltura_Client_Enum_SearchOperatorType::SEARCH_AND;
		$searchItem->metadataProfileId = self::getMrMetadataProfile();

		$conditions = array();
		$condition = new Kaltura_Client_Type_SearchMatchCondition();
		$condition->field = self::STATUS_XPATH_NAME;
		$condition->value = self::EXCLUDE;
		$condition->not = 1;
		$conditions[] = $condition;

		$searchItem->items = $conditions;
		return $searchItem;
	}

	private static function createMrStateConditionFilter($mrId, $statusLevel)
	{
		$condition = new Kaltura_Client_Type_SearchMatchCondition();
		$condition->field = self::MPRS_DATA_XPATH_NAME;
		$condition->value = "$mrId,$statusLevel";
		return $condition;
	}

	private static function createMrConditionFilter($mrId)
	{
		$condition = new Kaltura_Client_Type_SearchMatchCondition();
		$condition->field = self::MPRS_XPATH_NAME;
		$condition->value = "MR_$mrId";
		$condition->not = true;
		return $condition;
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

			case Kaltura_Client_ScheduledTask_Enum_ObjectTaskType::MAIL_NOTIFICATION:
				return new Kaltura_Client_ScheduledTask_Type_MailNotificationObjectTask();

			default:
				return null;
		}
	}

	public static function filterFactory($type) {
		switch($type)
		{
			case Kaltura_Client_ScheduledTask_Enum_ObjectFilterEngineType::ENTRY:
				return 'Kaltura_Client_Type_MediaEntryFilter';

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
		"8" => 'MAIL_NOTIFICATION',
		"scheduledTaskContentDistribution.Distribute" => 'DISTRIBUTE',
		"scheduledTaskEventNotification.DispatchEventNotification" => 'DISPATCH_EVENT_NOTIFICATION',
		"scheduledTaskMetadata.ExecuteMetadataXslt" => 'EXECUTE_METADATA_XSLT');
	//as from Kaltura_Client_ScheduledTask_Enum_ObjectTaskType
	public static function getDescriptionForType($type) {
		return self::$typeDescription[$type];
	}

	public static function addParamToObjectTask($objectTask, $params, $ignore = array()) {
		foreach ($params as $key => $value) {
			$objectTask->$key = $value;
		}
	}

}