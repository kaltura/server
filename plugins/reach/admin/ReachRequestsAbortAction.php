<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class ReachRequestsAbortAction extends KalturaApplicationPlugin
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

		$client = Infra_ClientHelper::getClient();
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($client);
		$kEntryVendorTask = new Kaltura_Client_Reach_Type_EntryVendorTask();
		$kEntryVendorTask->status = Kaltura_Client_Reach_Enum_EntryVendorTaskStatus::ABORTED;
		$kEntryVendorTask->errDescription = 'Aborted following abort request';

		$taskIds = $this->_getParam('task_ids');
		$taskIdsArr = explode(',', $taskIds);
		$maxTasks = 50;
		$abortResultArr = array();
		if (count($taskIdsArr) <= $maxTasks)
		{
			foreach ($taskIdsArr as $taskId)
			{
				try
				{
					$abortResult = $reachPluginClient->entryVendorTask->update($taskId, $kEntryVendorTask);
					$abortResultArr[] = 'Successfully updated task id: ' . $abortResult->id .
						' status: ' . $abortResult->status . ' err description: ' . $abortResult->errDescription;
				}
				catch (Exception $e)
				{
					KalturaLog::err('Error in entryVendorTask->update ' . $e->getMessage());
					$abortResultArr[] = 'Error: ' . $e->getMessage();
				}
			}
		}
		else
		{
			$abortResultArr[] = 'Could not handled more than ' . $maxTasks . ' tasks';
		}


		$action->view->abortResults = $abortResultArr;
	}
}
