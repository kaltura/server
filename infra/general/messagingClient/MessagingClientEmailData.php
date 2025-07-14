<?php
/**
 * @package infra
 * @subpackage messagingClient
 */

class MessagingClientEmailData
{
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
	public $templateId;

	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var string
	 */
	public $receiverType;

	/**
	 * @var array
	 */
	public $userIds;

	/**
	 * @var array
	 */
	public $msgParams;

	/**
	 * @param int $partnerId
	 * @param string $appGuid
	 * @param string $templateId
	 * @param array $userIds
	 * @param array $msgParams
	 */
	public function __construct($partnerId, $appGuid, $templateId, $userIds, $msgParams)
	{
		$this->partnerId = $partnerId;
		$this->appGuid = $appGuid;
		$this->templateId = $templateId;
		$this->type = 'email';
		$this->receiverType = 'user';
		$this->userIds = $userIds;
		$this->msgParams = $msgParams;
	}
}
