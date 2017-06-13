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
		$action->getHelper('layout')->disableLayout();
		$dryRunId = $this->_getParam('dryRunId');
		
		$action->view->formValid = false;
		$form  = new Form_MediaRepurposingLogs();
		try
		{
			if ($dryRunId) {
				$objects = MediaRepurposingUtils::getDryRunResult($dryRunId);
				$form->populateDryRun($dryRunId, $objects->objects);
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

