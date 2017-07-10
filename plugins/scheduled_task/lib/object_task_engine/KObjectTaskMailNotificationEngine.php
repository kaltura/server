<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectTaskEngine
 */
class KObjectTaskMailNotificationEngine
{
	const ENTRY_ID_PLACE_HOLDER = '{entry_id}';
	const PARTNER_ID_PLACE_HOLDER = '{partner_id}';

	private static function getAdminObjectsBody($objectsIds, $sendToUsers, $link = null)
	{
		$body = "\nExecute for entries: (users aggregate)\n";
		$cnt = 0;
		foreach($objectsIds as $userId => $entriesIds) {
			$body .= "[$userId]" . PHP_EOL;
			foreach($entriesIds as $id) {
				$link = $link ? str_replace(self::ENTRY_ID_PLACE_HOLDER, $id, $link) : '';
				$body .= "\t$id - $link" . PHP_EOL;
				$cnt++;
			}
		}
		$body .= "Total count of affected object: $cnt";

		if ($sendToUsers) {
			$body .= PHP_EOL . "Send Notification for the following users: ";
			foreach($objectsIds as $userId => $entriesIds)
				$body .= "$userId" . PHP_EOL;
		}
		return $body;
	}

	private static function getUserObjectsBody($objectsIds, $link)
	{
		$body = PHP_EOL ."Execute for entries:" . PHP_EOL;
		foreach($objectsIds as $id) {
			$link = $link ? str_replace(self::ENTRY_ID_PLACE_HOLDER, $id, $link) : '';
			$body .= "\t$id - $link" . PHP_EOL;
		}
		$body .= "Total count of affected object: " . count($objectsIds);
		return $body;
	}

	public static function sendMailNotification($mailTask, $objectsIds, $mediaRepurposingName, $partnerId)
	{
		$subject = $mailTask->subject;
		$sender = $mailTask->sender;

		$bodyPrefix = "Notification from Media Repurposing [$mediaRepurposingName]: \n$mailTask->message " . PHP_EOL;
		$link = $mailTask->link ? str_replace(self::PARTNER_ID_PLACE_HOLDER, $partnerId,  $mailTask->link) : null;
		$body = $bodyPrefix . self::getAdminObjectsBody($objectsIds, $mailTask->sendToUsers, $link);

		$toArr = explode(",", $mailTask->mailAddress);
		$success = self::sendMail($toArr, $subject, $body, $sender);
		if (!$success)
			KalturaLog::info("Mail for MRP [$mediaRepurposingName] did not send successfully");

		if ($mailTask->sendToUsers)
			foreach ($objectsIds as $user => $objects) {
				$body = $bodyPrefix . self::getUserObjectsBody($objects, $link);
				$success = self::sendMail(array($user), $subject, $body, $sender);
				if (!$success)
					KalturaLog::info("Mail for MRP [$mediaRepurposingName] did not send successfully");
			}
	}

	public static function sendMail($toArray, $subject, $body, $sender = null)
	{
		$mailer = new PHPMailer();
		$mailer->CharSet = 'utf-8';
		if (!$toArray || count($toArray) < 1 || strlen($toArray[0]) == 0)
			return true;
		foreach ($toArray as $to)
			$mailer->AddAddress($to);
		$mailer->Subject = $subject;
		$mailer->Body = $body;

		//$mailer->From = '';
		$mailer->FromName = $sender;

		KalturaLog::info("sending mail to " . implode(",",$toArray) . " with body: $body");
		return $mailer->Send();
	}

}