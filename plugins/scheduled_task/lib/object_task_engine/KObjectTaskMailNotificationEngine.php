<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectTaskEngine
 */
class KObjectTaskMailNotificationEngine
{
	const ENTRY_ID_PLACE_HOLDER = '{entry_id}';
	const PARTNER_ID_PLACE_HOLDER = '{partner_id}';
	const ENTRIES_ID_AND_NAME = 'entriesIdAndName';
	const EMAIL = 'email';
	const ENTRY_NAME = 'EntryName';
	const ENTRY_ID = 'entryId';

	private static function getAdminObjectsBody($objectsData, $sendToUsers, $link = null)
	{
		$body = "Execute for entries:".PHP_EOL;
		$cnt = 0;
		foreach($objectsData as $userId => $data)
		{
			$entriesIdsAndNames = $data[self::ENTRIES_ID_AND_NAME];
			foreach($entriesIdsAndNames as $entryIdAndName)
			{
				$id = $entryIdAndName[self::ENTRY_ID];
				$name = $entryIdAndName[self::ENTRY_NAME];
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
			$id = $entryIdAndName[self::ENTRY_ID];
			$name = $entryIdAndName[self::ENTRY_NAME];
			$readyLink = $link ? " - ".str_replace(self::ENTRY_ID_PLACE_HOLDER, $id, $link) : '';
			$body.="\t$name Id:$id $readyLink".PHP_EOL;
			$cnt++;
		}

		$body .= "Total count of affected object: ".$cnt;
		return $body;
	}

	/**
	 * @param $mailTask
	 * @param array $userObjectsDataMap
	 * @param string $mediaRepurposingId
	 * @param string $partnerId
	 */
	public static function sendMailNotification($mailTask, $userObjectsDataMap, $mediaRepurposingId, $partnerId, $client = null )
	{
		$subject = $mailTask->subject;
		$sender = $mailTask->sender;
		$link = $mailTask->link ? str_replace(self::PARTNER_ID_PLACE_HOLDER, $partnerId,  $mailTask->link) : null;
		$body = $mailTask->message.PHP_EOL.self::getAdminObjectsBody($userObjectsDataMap, $mailTask->sendToUsers, $link);
		$body = $mailTask->footer ? $body.PHP_EOL.$mailTask->footer : $body;
		$toArr = explode(",", $mailTask->mailTo);
		$success = self::sendMail($toArr, $subject, $body, $sender);
		if (!$success)
			KalturaLog::info("Mail for MRP [$mediaRepurposingId] did not send successfully");

		if ($mailTask->sendToUsers)
		{
			foreach ($userObjectsDataMap as $user => $data)
			{
				$body = $mailTask->message . PHP_EOL . self::getUserObjectsBody($data[self::ENTRIES_ID_AND_NAME], $link);
				$body = $mailTask->footer ? $body.PHP_EOL.$mailTask->footer : $body;

				if($data[self::EMAIL])
				{
					$success = self::sendMail(array($data[self::EMAIL]), $subject, $body, $sender);
					if (!$success)
						KalturaLog::info("Mail for MRP [$mediaRepurposingId] did not send successfully");
				}
				else
					KalturaLog::info("Mail for MRP [$mediaRepurposingId] did not send successfully for user [$user] missing valid email.");
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