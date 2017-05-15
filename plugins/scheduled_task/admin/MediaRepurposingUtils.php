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
	const ADMIN_CONSOLE_PARTNER = "-2";
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
		$client->setPartnerId(self::ADMIN_CONSOLE_PARTNER);

		return ($result->objects[0]->status == Kaltura_Client_Enum_PermissionStatus::ACTIVE);
	}


	public static function getPluginByName($name, $partnerId = null) {
		$client = Infra_ClientHelper::getClient();
		if ($partnerId)
			$client->setPartnerId($partnerId);
		else
			$client->setPartnerId(self::ADMIN_CONSOLE_PARTNER);
		$plugin = $name::get($client);
		return $plugin;
	}



	public static function changeMrStatus($mr, $newStatus) {
		$scheduledtaskPlugin = self::getPluginByName('Kaltura_Client_ScheduledTask_Plugin');

		$scheduledTaskProfile = new Kaltura_Client_ScheduledTask_Type_ScheduledTaskProfile();
		$scheduledTaskProfile->status = $newStatus;

		$scheduleTaskIds = explode(',', $mr->description);
		KalturaLog::info("starting changing status of media repurpesing and its schedule tasks [$mr->description] to $newStatus");
		foreach ($scheduleTaskIds as $scheduleTaskId)
			if ($scheduleTaskId)
				$result = $scheduledtaskPlugin->scheduledTaskProfile->update($scheduleTaskId, $scheduledTaskProfile);

		$mr->status = $newStatus;
		$result = $scheduledtaskPlugin->scheduledTaskProfile->update($mr->id, $scheduledTaskProfile);
	}

	
	public static function createNewMr($name, $filterTypeEngine, $filter, $taskArray, $partnerId, $maxEntriesAllowed) {
		$mr = self::createScheduleTask($partnerId, $name, $filterTypeEngine, $filter, $taskArray[0], $maxEntriesAllowed);
		$mr->systemName = self::MEDIA_REPURPOSING_SYSTEM_NAME;
		$scheduledTaskPlugin = self::getPluginByName('Kaltura_Client_ScheduledTask_Plugin', $partnerId);
		$result = $scheduledTaskPlugin->scheduledTaskProfile->add($mr);

		$mrId = $result->id;
		$mr->description = self::handleScheduleTasks($partnerId, $mrId, $name, $filterTypeEngine, $filter, $taskArray, $maxEntriesAllowed);
		$mr->objectFilter->advancedSearch->items[0]->items[] = self::createMrConditionFilter($mrId);
		$scheduledTaskPlugin = self::getPluginByName('Kaltura_Client_ScheduledTask_Plugin', $partnerId);
		return $scheduledTaskPlugin->scheduledTaskProfile->update($mrId, $mr);
	}

	
	public static function UpdateMr($id, $name, $filterTypeEngine, $filter, $taskArray, $partnerId, $maxEntriesAllowed)
	{
		$taskArray[0]->relatedObjects = null;
		$mr = self::createScheduleTask($partnerId, $name, $filterTypeEngine, $filter, $taskArray[0], $maxEntriesAllowed);
		$mr->systemName = self::MEDIA_REPURPOSING_SYSTEM_NAME;
		$mr->objectFilter->advancedSearch->items[0]->items[] = self::createMrConditionFilter($id);
		$mr->description = self::handleScheduleTasks($partnerId, $id, $name, $filterTypeEngine, clone($filter), $taskArray, $maxEntriesAllowed);

		$scheduledtaskPlugin = self::getPluginByName('Kaltura_Client_ScheduledTask_Plugin', $partnerId);
		return $scheduledtaskPlugin->scheduledTaskProfile->update($id, $mr);

	}

	private static function handleScheduleTasks($partnerId, $mrId, $name, $filterTypeEngine, $filter, $taskArray, $maxEntriesAllowed)
	{
		$ids = '';
		for ($i = 2; $i < count($taskArray); $i += 2) {
			$sdId = $taskArray[$i]->relatedObjects;
			$taskArray[$i]->relatedObjects = null;

			$timeAfterLast = $taskArray[$i-1];
			$stName = self::getSubScheduleTaskName($name, $i);

			$scheduledTaskProfile = self::createScheduleTask($partnerId, $stName, $filterTypeEngine, $filter, $taskArray[$i], $maxEntriesAllowed);
			$scheduledTaskProfile->description = $timeAfterLast;
			$scheduledTaskProfile->objectFilter->advancedSearch->items[0]->items[] = self::createMrStateConditionFilter($mrId, ($i/2));

			KalturaLog::info("Handle Schedule Task [$sdId] who should run [$timeAfterLast] days after last Schedule Task:");
			KalturaLog::info(print_r($scheduledTaskProfile, true));

			//add task to API
			$scheduledTaskPlugin = self::getPluginByName('Kaltura_Client_ScheduledTask_Plugin', $partnerId);
			if ($sdId)
				$result = $scheduledTaskPlugin->scheduledTaskProfile->update($sdId, $scheduledTaskProfile);
			else
				$result = $scheduledTaskPlugin->scheduledTaskProfile->add($scheduledTaskProfile);

			if (strlen($ids))
				$ids.= ",";
			$ids.= $result->id . "[$timeAfterLast]";
		}
		return $ids;
	}


	private static function createScheduleTask($partnerId, $name, $filterTypeEngine, $filter, $task, $maxEntriesAllowed)
	{
		$scheduleTask = new Kaltura_Client_ScheduledTask_Type_ScheduledTaskProfile();
		$scheduleTask->name = $name;
		$scheduleTask->status = ScheduledTaskProfileStatus::DISABLED;

		if (!$filter->advancedSearch)
			$filter->advancedSearch = self::createSearchOperator();
		array_unshift($filter->advancedSearch->items, self::createMRFilterForStatus($partnerId));

		$scheduleTask->objectFilter = $filter;
		$scheduleTask->objectFilterEngineType = $filterTypeEngine;
		if (!$filterTypeEngine)
			$scheduleTask->objectFilterEngineType = ObjectFilterEngineType::ENTRY;
		$scheduleTask->maxTotalCountAllowed = $maxEntriesAllowed;
		$scheduleTask->objectTasks = array($task);
		return $scheduleTask;
	}


	private static function getMrMetadataProfile($partnerId)
	{
		$filter = new Kaltura_Client_Metadata_Type_MetadataProfileFilter();
		$filter->systemNameEqual = 'MRP';
		$filter->partnerIdEqual = $partnerId;
		$metadataPlugin = self::getPluginByName('Kaltura_Client_Metadata_Plugin');
		$res = $metadataPlugin->metadataProfile->listAction($filter, null);
		if ($res->totalCount != 1)
			return null;
		return $res->objects[0];
	}


	private static function createMRFilterForStatus($partnerId)
	{
		$searchItem = new Kaltura_Client_Metadata_Type_MetadataSearchItem();
		$searchItem->type = Kaltura_Client_Enum_SearchOperatorType::SEARCH_AND;

		$profile = self::getMrMetadataProfile($partnerId);
		if (!$profile)
			return null;
		$searchItem->metadataProfileId = $profile->id;

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
	
	public static function createSearchOperator($metadataSearchArray = array()) {
		$searchOperator = new Kaltura_Client_Type_SearchOperator();
		$searchOperator->type = Kaltura_Client_Enum_SearchOperatorType::SEARCH_AND;
		$searchOperator->items = $metadataSearchArray;
		return $searchOperator;
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

	private static function getTypeOfVar($obj, $name)
	{
		$reflectClass = new ReflectionClass(get_class($obj));
		$property = $reflectClass->getProperty($name);
		return ConfigureForm::getTypeFromDoc($property->getDocComment());
	}
	
	public static function addParamToObjectTask($objectTask, $params, $ignore = array())
	{
		foreach ($params as $key => $value) {
			$type = self::getTypeOfVar($objectTask, $key);
			$objectTask->$key = self::getValueFromString($value, $type);
		}
	}

	public static function getParamToTask($task, $ignore = array()) {
		$params = array();
		foreach ($task as $key => $value) {
			$type = self::getTypeOfVar($task, $key);
			if (!in_array($key, $ignore))
				$params[$key] = self::setValueToString($value, $type);
		}
		return $params;
	}

	private static function elementTypeFactory($type)
	{
		switch ($type) {
			case 'KalturaIntegerValue':
				return new Kaltura_Client_Type_IntegerValue();
			default:
				return null;
		}
	}

	private static function getValueFromString($value, $type) {
		if (strpos($type ,'array') > -1)
		{
			$arr = array();
			$elemType = explode(" ", $type); // template is 'array of XXX';
			foreach(explode(",", $value) as $val) {
				$elem = self::elementTypeFactory($elemType[2]);
				$elem->value = intval($val);
				$arr[] = $elem;
			}
			return $arr;
		}
		return $value;
	}

	private static function setValueToString($value, $type) {
		if (strpos($type ,'array') > -1)
		{
			$values = '';
			foreach($value as $val)
				$values .= $val->value . ",";
			return rtrim($values, ',');
		}
		return $value;
	}
	

}