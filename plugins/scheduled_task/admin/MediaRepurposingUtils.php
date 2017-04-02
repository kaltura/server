<?php

/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */

class MediaRepurposingUtils
{
	const SCHEDULE_TASK_NAME_PREFIX = 'MR_';
	/**
	 * get all Media Repurposing of the partner
	 * @param int $partnerId
	 * @return array of KalturaMediaRepurposingProfile
	 */
	public static function getMrs($partnerId) {
		if (!$partnerId)
			return array();
		$systemPartnerPlugin = self::getPluginByName('Kaltura_Client_SystemPartner_Plugin');
		$result = $systemPartnerPlugin->systemPartner->get($partnerId);

		$show = array();
		foreach ($result->mrProfiles as $mr)
			if ($mr->status != Kaltura_Client_ScheduledTask_Enum_ScheduledTaskProfileStatus::DELETED)
				$show[] = $mr;
		return $show;
	}

	/**
	 * update list of  Media Repurposing on the partner
	 * @param int $partnerId
	 * @param array $mediaRepurposingProfiles of KalturaMediaRepurposingProfile
	 */
	public static function updateMrs($partnerId, $mediaRepurposingProfiles) {
		$conf = new Kaltura_Client_SystemPartner_Type_SystemPartnerConfiguration();
		$conf->mrProfiles = $mediaRepurposingProfiles;

		$systemPartnerPlugin = self::getPluginByName('Kaltura_Client_SystemPartner_Plugin');
		$result = $systemPartnerPlugin->systemPartner->updateConfiguration($partnerId, $conf);
	}

	private static function getPluginByName($name, $partnerId = null) {
		$client = Infra_ClientHelper::getClient();
		if ($partnerId)
			$client->setPartnerId($partnerId);
		$plugin = $name::get($client);
		return $plugin;
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

	/**
	 * check if name if already exist in the KalturaMediaRepurposingProfile list
	 * @param string $name
	 * @param array $mediaRepurposingProfiles of KalturaMediaRepurposingProfile
	 * @return boolean in exist
	 */
	public static function checkForNameInMRs($name, $mediaRepurposingProfiles) {
		foreach ($mediaRepurposingProfiles as $mr) {
			if ($mr->name == $name)
				return false;
		}
		return true;
	}


	/**
	 * updated the status of the mr and all its schedule tasks
	 * @param KalturaMediaRepurposingProfile $mr
	 * @param int $newStatus
	 * @return KalturaMediaRepurposingProfile updated
	 */
	public static function changeMrStatus($mr, $newStatus) {
		$scheduledtaskPlugin = self::getPluginByName('Kaltura_Client_ScheduledTask_Plugin');

		$scheduledTaskProfile = new Kaltura_Client_ScheduledTask_Type_ScheduledTaskProfile();
		$scheduledTaskProfile->status = $newStatus;

		$scheduleTaskIds = explode(',', $mr->scheduleTasksIds);
		KalturaLog::info("starting changing status of media repurpesing ans its ST [$mr->scheduleTasksIds] to $newStatus");
		foreach ($scheduleTaskIds as $scheduleTaskId)
			$result = $scheduledtaskPlugin->scheduledTaskProfile->update($scheduleTaskId, $scheduledTaskProfile);

		$mr->status = $newStatus;
		return $mr;
	}

	/**
	 * @param string $type
	 * @return Kaltura_Client_ScheduledTask_Type_ObjectTask or null
	 */
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

	/**
	 * @param string $type
	 * @return Kaltura_Client_Type_Filter or null
	 */
	public static function filterFactory($type) {
		switch($type)
		{
			case Kaltura_Client_ScheduledTask_Enum_ObjectFilterEngineType::ENTRY:
				return 'Kaltura_Client_Type_BaseEntryFilter';
			
			default:
				return null;
		}
	}
	
	public static function addParamToObjectTask($objectTask, $params) {
		foreach ($params as $key => $value) {
			$objectTask->$key = $value;
		}
	}

	public static function getScheduleTaskById($scheduledTaskProfileId) {
		$scheduledtaskPlugin = self::getPluginByName('Kaltura_Client_ScheduledTask_Plugin');
		$result = $scheduledtaskPlugin->scheduledTaskProfile->get($scheduledTaskProfileId);
		return $result;
	}

	/**
	 * create new Media Repurposing and all its schedule task
	 * @param string $name
	 * @param int $type
	 * @param Kaltura_Client_Type_BaseEntryFilter $filter
	 * @return KalturaMediaRepurposingProfile
	 */
	public static function createNewMr($name, $type, $filter, $statusChanges, $extraParamForType, $partnerId) {
		$mr = new Kaltura_Client_ScheduledTask_Type_MediaRepurposingProfile();
		$mr->name = $name;
		$mr->taskType = $type;
		$mr->status = ScheduledTaskProfileStatus::DISABLED;
		$mr->objectFilter = $filter;

		$scheduledtaskPlugin = self::getPluginByName('Kaltura_Client_ScheduledTask_Plugin', $partnerId);
		$statusChangesArray = explode(',', $statusChanges);

		$count = "1"; //count($statusChangesArray) /2;


		$scheduledTaskProfile = new Kaltura_Client_ScheduledTask_Type_ScheduledTaskProfile();
		$scheduledTaskProfile->name = self::SCHEDULE_TASK_NAME_PREFIX .$name .'_'.$count;
		$scheduledTaskProfile->objectFilter = $filter;
		$scheduledTaskProfile->objectFilterEngineType = Kaltura_Client_ScheduledTask_Enum_ObjectFilterEngineType::ENTRY;
		$scheduledTaskProfile->maxTotalCountAllowed = 500;
		$scheduledTaskProfile->objectTasks = array();


		$task = self::objectTaskFactory($type);
		self::addParamToObjectTask($task, $extraParamForType);
		$scheduledTaskProfile->objectTasks[0] = $task;





		//create final task
		$result = $scheduledtaskPlugin->scheduledTaskProfile->add($scheduledTaskProfile);
		$ids = "$result->id";

		//KalturaLog::info("asdf - 10");
		//KalturaLog::info(print_r($result,true));
		//KalturaLog::info($result->id);

		KalturaLog::info("asdf - 10");
		KalturaLog::info(print_r($statusChanges,true));

		KalturaLog::info("qwer");
		for ($i = 0; $i < count($statusChangesArray); $i+=2) {
			if ($statusChangesArray[$i]) {
				$notifiy = $statusChangesArray[$i+1];
				KalturaLog::info("add after $statusChangesArray[$i] day and notifiy [$notifiy]");
			}
		}



//		foreach ($alarms as $alarm) {
//			//create ST
//			$ids.= ",$result->id";
//		}
		//TODO - add all schedule tasks and and ID to $mr
		//$mr->scheduleTasksIds = "12n,13y";
		$mr->scheduleTasksIds = $ids;


		return $mr;
	}
	
}