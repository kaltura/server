<?php
/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class MediaRepurposingSetStatusAction extends KalturaApplicationPlugin
{
	
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
		$mediaRepurposingId = $this->_getParam('mediaRepurposingId');
		$newStatus = $this->_getParam('mediaRepurposingStatus');

		$mr = MediaRepurposingUtils::getMrById($mediaRepurposingId);
		MediaRepurposingUtils::changeMrStatus($mr, $newStatus);

		$batchJobId = null;
		if ($newStatus == Kaltura_Client_ScheduledTask_Enum_ScheduledTaskProfileStatus::DRY_RUN_ONLY)
		{
			$batchJobId = MediaRepurposingUtils::executeDryRun($mr);
			KalturaLog::info("Add job for Schedule Task Dry Run with ID $batchJobId. Data will save in configured path [default: {WEB_DIR_PATH}/content/batchfiles/{PARTNER_ID}/bulk_$batchJobId");
			MediaRepurposingUtils::changeMrStatus($mr, Kaltura_Client_ScheduledTask_Enum_ScheduledTaskProfileStatus::DISABLED);
		}
		
		try
		{
			echo $action->getHelper('json')->sendJson(array('ok',$batchJobId), false);
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			echo $action->getHelper('json')->sendJson($e->getMessage(), false);
		}

	}
}

