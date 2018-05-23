<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectTaskEngine
 */
class KReachMailNotificationEngine extends KObjectTaskMailNotificationEngine
{
	const VENDOR_SERVICE_TYPE = 'VendorServiceType';
	const VENDOR_SERVICE_FEATURE = 'VendorServiceFeature';
	const VENDOR_SERVICE_TURN_AROUND_TIME = 'VendorServiceTurnAroundTime';

	private static function getAdminObjectsBody($objectsData, $sendToUsers, $link = null, $client = null)
	{
		$reachPlugin = KalturaReachClientPlugin::get($client);
		$body = "Awaiting tasks for approval:".PHP_EOL;
		$cnt = 0;
		$constantsMap = self::loadConstantsNames();
		foreach($objectsData as $entryVendorTask )
		{
			$cnt++;
			$entryVendorTaskId = $entryVendorTask->id;
			/* @var KalturaEntryVendorTask $entryVendorTask */
			$id = $entryVendorTask->entryId;
			try{
				$entry = $client->baseEntry->get($id);
				$catalogItem = $reachPlugin->vendorCatalogItem->get($entryVendorTask->catalogItemId);
			}
			catch (KalturaException $e)
			{
				KalturaLog::err($e);
				if ( $e->getCode() == 'ENTRY_ID_NOT_FOUND')
				{
					KalturaLog::err("Could not find entry [$id] for entryVendorTask [$entryVendorTaskId]");
					$body .= "\t Task Id: $entryVendorTaskId - entry with id $id doesn't exist for task. ";
				}
				elseif ( $e->getCode() == 'CATALOG_ITEM_NOT_FOUND')
				{
					KalturaLog::err("Could not find catalog item $entryVendorTask->catalogItemId for entryVendorTask $entryVendorTaskId");
					$body .= "\t Task Id: $entryVendorTaskId - entry with catalog item id $entryVendorTask->catalogItemId doesn't exist for task. ";
				}
				continue;
			}

			$name = $entry->name;
			$requester = $entryVendorTask->userId;
			$requestDate = $entryVendorTask->createdAt;

			/* @var KalturaVendorCatalogItem $catalogItem */
			$serviceType = array_search($catalogItem->serviceType, $constantsMap[self::VENDOR_SERVICE_TYPE]);
			$turnAroundTime = array_search($catalogItem->turnAroundTime, $constantsMap[self::VENDOR_SERVICE_TURN_AROUND_TIME]);
			$serviceFeature = array_search($catalogItem->serviceFeature, $constantsMap[self::VENDOR_SERVICE_FEATURE]);

			$body .= "\tTask Ud: $entryVendorTaskId - Video Name:$name - Video Id: $id - requester: $requester - Requested date: $requestDate - $serviceType $serviceFeature with turn around time of: $turnAroundTime" . PHP_EOL;
		}
		$body.= "\n\tTotal count of awaiting tasks for approval: $cnt";
		return $body;
	}

	private static function loadConstantsNames()
	{
		$classes = array ('VendorServiceType', 'VendorServiceFeature' , 'VendorServiceTurnAroundTime' , 'VendorServiceFeature');
		$data = array();
		foreach ($classes as $className )
		{
			$class = new ReflectionClass ($className);
			$constants = $class->getConstants();
			$constName = null;
			$data[$className] = array();
			foreach ($constants as $name => $value)
			{
				$data[$className][$name] = $value;
			}
		}
		return $data;
	}

	/**
	 * @param $mailTask
	 * @param array $entryVendorTasks
	 * @param string $reachProfileId
	 * @param string $partnerId
	 */
	public static function sendMailNotification($mailTask, $entryVendorTasks, $reachProfileId, $partnerId, $client = null )
	{
		$subject = $mailTask->subject;
		$sender = $mailTask->sender;
		$link = $mailTask->link ? str_replace(self::PARTNER_ID_PLACE_HOLDER, $partnerId,  $mailTask->link) : null;
		$body = $mailTask->message.PHP_EOL.self::getAdminObjectsBody($entryVendorTasks, $mailTask->sendToUsers, $link, $client);
		$body = $mailTask->footer ? $body.PHP_EOL.$mailTask->footer : $body;
		$toArr = explode(",", $mailTask->mailTo);
		$success = self::sendMail($toArr, $subject, $body, $sender);
		if (!$success)
			KalturaLog::info("Mail for Reach Profile [$reachProfileId] did not send successfully");
	}
}