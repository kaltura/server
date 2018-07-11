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
				$results = MediaRepurposingUtils::getDryRunResult($dryRunId);
				$adapter = new Kaltura_FilterPaginatorList($results->objects);
				$action->view->paginator = new Infra_Paginator($adapter, $request);
				$action->view->scheme = $this->getScheme($results->objects);
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

	private function getScheme($objects)
	{
		if (empty($objects))
			return array();
		return ScheduleTaskUtils::getSchemeMap($objects[0]);
	}

}