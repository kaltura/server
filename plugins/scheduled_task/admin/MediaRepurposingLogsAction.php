<?php
/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class MediaRepurposingLogsAction extends KalturaApplicationPlugin
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
		$action->getHelper('layout')->setLayout('layout_empty');
		$request = $action->getRequest();
		$dryRunId = $this->_getParam('dryRunId');
		
		$action->view->scheme = array();
		$form  = new Form_MediaRepurposingLogs();
		try
		{
			if ($dryRunId) {
				$adapter = new Kaltura_FilterPaginatorForDryRunResult($dryRunId);
				$action->view->paginator = new Infra_Paginator($adapter, $request);
				$action->view->scheme = array("id", "name", "userId", "views", "createdAt", "lastPlayedAt");
				$form->populateDryRun($dryRunId, $adapter->count());
			}
			
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
		}
		$action->view->form = $form;
	}


}

