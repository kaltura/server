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

		try
		{
			echo $action->getHelper('json')->sendJson('ok', false);
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			echo $action->getHelper('json')->sendJson($e->getMessage(), false);
		}

	}
}

