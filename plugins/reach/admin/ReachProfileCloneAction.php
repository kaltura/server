<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class ReachProfileCloneAction extends KalturaApplicationPlugin
{
	const ADMIN_CONSOLE_RULE_PREFIX = "AutomaticAdminConsoleRule_";
	const EMPTY_STRING = 'N/A';

	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}

	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('layout')->disableLayout();
		$reachProfileId = $this->_getParam('reachProfileId');
		$partnerId = $this->_getParam('partnerId');

		$client = Infra_ClientHelper::getClient();

		try
		{
			$reachPluginClient = Kaltura_Client_Reach_Plugin::get($client);
			$reachProfile = $reachPluginClient->reachProfile->get($reachProfileId);
			if (!$reachProfile)
			{
				$msg = "Reach Profile id not found $reachProfileId";
				echo $action->getHelper('json')->sendJson($msg , false);
			}
			else
			{
				$shouldCopyAllRules = false;
				if ($partnerId == $reachProfile->partnerId)
				{
					$shouldCopyAllRules = true;
				}
				$reachProfile->id = null;
				$reachProfile->partnerId = null;
				$reachProfile->name = "[$partnerId] $reachProfile->name";
				$reachProfile->dictionaries = null;
				$reachProfile->createdAt = null;
				$reachProfile->updatedAt = null;
				$reachProfile->usedCredit = null;
				$reachProfile->status = null;

				if (!$shouldCopyAllRules)
				{
					foreach ( $reachProfile->rules as $key => $rule )
					{
						if (empty($rule->description)
							|| substr($rule->description, 0, strlen(self::ADMIN_CONSOLE_RULE_PREFIX)) !== self::ADMIN_CONSOLE_RULE_PREFIX
							|| self::gotBooleanEventNotificationCondition($rule))
						{
							unset($reachProfile->rules[$key]);
						}
					}
				}

				Infra_ClientHelper::impersonate($partnerId);
				$reachPluginClient->reachProfile->add($reachProfile);
				echo $action->getHelper('json')->sendJson('ok', false);
			}
		}
		catch (Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			echo $action->getHelper('json')->sendJson($e->getMessage(), false);
		}
		Infra_ClientHelper::unimpersonate();
	}

	protected function gotBooleanEventNotificationCondition($rule)
	{

		foreach ($rule->conditions as $condition)
		{
			if (isset($condition->booleanEventNotificationIds) && $condition->booleanEventNotificationIds && $condition->booleanEventNotificationIds !== self::EMPTY_STRING)
			{
				return true;
			}
		}
		return false;
	}
}