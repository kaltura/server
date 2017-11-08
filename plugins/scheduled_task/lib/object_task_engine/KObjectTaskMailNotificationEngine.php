<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectTaskEngine
 */
class KObjectTaskMailNotificationEngine
{
	const ENTRY_ID_PLACE_HOLDER = '{entry_id}';
	const PARTNER_ID_PLACE_HOLDER = '{partner_id}';

	private static function getAdminObjectsBody($objectsData, $sendToUsers, $link = null)
	{
		$body = "Execute for entries:".PHP_EOL;
		$cnt = 0;
		foreach($objectsData as $userId => $entriesIdsAndNames)
		{
			foreach($entriesIdsAndNames as $entryIdAndName)
			{
				$id = $entryIdAndName['id'];
				$name = $entryIdAndName['name'];
				$readyLink = $link ? " - ".str_replace(self::ENTRY_ID_PLACE_HOLDER, $id, $link) : '';
				$body.="\t$name Id:$id $readyLink".PHP_EOL;
				$cnt++;
			}
		}

		$body.= "Total count of affected object: $cnt";
		if ($sendToUsers)
		{
			$body .= PHP_EOL."Send Notification for the following users: ";
			foreach($objectsData as $userId => $entriesIdsAndNames)
			{
				$body .= "$userId".PHP_EOL;
			}
		}

		return $body;
	}

	private static function getUserObjectsBody($objectsData, $link)
	{
		$body =  "Execute for entries:" . PHP_EOL;
		$cnt = 0;
		foreach($objectsData as $entryIdAndName)
		{
			$id = $entryIdAndName['id'];
			$name = $entryIdAndName['name'];
			$readyLink = $link ? " - ".str_replace(self::ENTRY_ID_PLACE_HOLDER, $id, $link) : '';
			$body.="\t$name Id:$id $readyLink".PHP_EOL;
			$cnt++;
		}

		$body .= "Total count of affected object: ".$cnt;
		return $body;
	}

	public static function sendMailNotification($mailTask, $userObjectsDataMap, $mediaRepurposingId, $partnerId)
	{
		$subject = $mailTask->subject;
		$sender = $mailTask->sender;
		$link = $mailTask->link ? str_replace(self::PARTNER_ID_PLACE_HOLDER, $partnerId,  $mailTask->link) : null;
		$body = $mailTask->message.PHP_EOL.self::getAdminObjectsBody($userObjectsDataMap, $mailTask->sendToUsers, $link);
		$body = $mailTask->footer ? $body.message.PHP_EOL.$mailTask->footer : $body;
		$toArr = explode(",", $mailTask->mailTo);
		$success = self::sendMail($toArr, $subject, $body, $sender);
		if (!$success)
			KalturaLog::info("Mail for MRP [$mediaRepurposingId] did not send successfully");

		if ($mailTask->sendToUsers)
		{
			foreach ($userObjectsDataMap as $user => $objects)
			{
				$body = $mailTask->message . PHP_EOL . self::getUserObjectsBody($objects, $link);
				$body = $mailTask->footer ? $body.message.PHP_EOL.$mailTask->footer : $body;
				$success = self::sendMail(array($user), $subject, $body, $sender);
				if (!$success)
					KalturaLog::info("Mail for MRP [$mediaRepurposingId] did not send successfully");
			}
		}
	}

	public static function sendMail($toArray, $subject, $body, $sender = null)
	{
		$mailer = new PHPMailer();
		$mailer->CharSet = 'utf-8';
		$mailer->Mailer = 'smtp';
		$mailer->SMTPKeepAlive = true;

		if (!$toArray || count($toArray) < 1 || strlen($toArray[0]) == 0)
			return true;

		foreach ($toArray as $to)
			$mailer->AddAddress($to);

		$mailer->Subject = $subject;
		$mailer->Body = $body;
		$mailer->Sender = KAsyncMailer::MAILER_DEFAULT_SENDER_EMAIL;
		$mailer->From = KAsyncMailer::MAILER_DEFAULT_SENDER_EMAIL;
		$mailer->FromName = $sender;

		KalturaLog::info("sending mail to " . implode(",",$toArray) . ", from: [$sender]. subject: [$subject] with body: [$body]");
		try
		{
			return $mailer->Send();
		}
		catch ( Exception $e )
		{
			KalturaLog::err( $e );
			return false;
		}
	}
}