<?php
/**
 * @package infra
 * @subpackage general
 */
class kSendMail
{
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