<?php

/**
 * @package plugins.reach
 * @subpackage model
 */
class kImmersiveAgentCallVendorTaskData extends kVendorTaskData
{
	public string $callId = "";

	public function getCallId(): string
	{
		return $this->callId;
	}

	public function setCallId(string $callId): void
	{
		$this->callId = $callId;
	}
}
