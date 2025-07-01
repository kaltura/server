<?php
/**
 * @package infra
 * @subpackage messagingClient
 */

class MessagingClientEmailTemplate
{
	public $partnerId;
	public $appGuid;
	public $internalName;
	public $name;
	public $description;
	public $toAttributePath;
	public $status;
	public $from;
	public $cc;
	public $bcc;
	public $subject;
	public $body;
	public $msgParamsMap;

	public function __construct(
		$partnerId,
		$appGuid,
		$internalName,
		$name,
		$description,
		$from,
		$cc,
		$bcc,
		$subject,
		$body,
		$msgParamsMap
	)
	{
		$this->partnerId = $partnerId;
		$this->appGuid = $appGuid;
		$this->internalName = $internalName;
		$this->name = $name;
		$this->description = $description;
		$this->toAttributePath = '{user.email}';
		$this->status = 'enabled';
		$this->from = $from;
		$this->cc = $cc;
		$this->bcc = $bcc;
		$this->subject = $subject;
		$this->body = $body;
		$this->msgParamsMap = $msgParamsMap;
	}
}
