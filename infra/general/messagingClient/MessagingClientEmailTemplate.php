<?php
/**
 * @package infra
 * @subpackage messagingClient
 */

class MessagingClientEmailTemplate
{
	/**
	 * @var int
	 */
	public $id;

	/**
	 * @var int
	 */
	public $partnerId;

	/**
	 * @var string
	 */
	public $appGuid;

	/**
	 * @var string
	 */
	public $internalName;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var string
	 */
	public $toAttributePath;

	/**
	 * @var string
	 */
	public $status;

	/**
	 * @var string
	 */
	public $from;

	/**
	 * @var string
	 */
	public $cc;

	/**
	 * @var string
	 */
	public $bcc;

	/**
	 * @var string
	 */
	public $subject;

	/**
	 * @var string
	 */
	public $body;

	/**
	 * @var array
	 */
	public $msgParamsMap;

	/**
	 * @param int $partnerId
	 * @param string $appGuid
	 * @param string $internalName
	 * @param string $name
	 * @param string $description
	 * @param string $from
	 * @param string $cc
	 * @param string $bcc
	 * @param string $subject
	 * @param string $body
	 * @param array $msgParamsMap
	 */
	public function __construct($partnerId, $appGuid, $internalName, $name, $description, $from, $cc, $bcc, $subject, $body, $msgParamsMap)
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
