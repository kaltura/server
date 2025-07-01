<?php
/**
 * @package infra
 * @subpackage messagingClient
 */

class MessagingClientEmailData
{
	public $partnerId;
	public $appGuid;
	public $templateId;
	public $type;
	public $receiverType;
	public $userIds;
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
