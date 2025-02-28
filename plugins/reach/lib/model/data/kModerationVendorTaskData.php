<?php

/**
 * @package plugins.reach
 * @subpackage model
 */
class kModerationVendorTaskData extends kVendorTaskData
{
	protected string $ruleIds = "";
	protected string $policyIds = "";
	protected string $moderationOutputJson = "";

	public function getRuleIds(): string
	{
		return $this->ruleIds;
	}

	public function setRuleIds(string $ruleIds): void
	{
		$this->ruleIds = $ruleIds;
	}

	public function getPolicyIds(): string
	{
		return $this->policyIds;
	}

	public function setPolicyIds(string $policyIds): void
	{
		$this->policyIds = $policyIds;
	}

	public function getModerationOutputJson(): string
	{
		return $this->moderationOutputJson;
	}

	public function setModerationOutputJson(string $moderationOutputJson): void
	{
		$this->moderationOutputJson = $moderationOutputJson;
	}
}
