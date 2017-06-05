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
		$partnerId = $this->_getParam('partner_id');
		$mediaRepurposingId = $this->_getParam('mr_Id');
		$startDate = $this->_getParam('start_date');
		$endDate = $this->_getParam('end_date');
		
		$mediaRepurposingLogForm = new Form_MediaRepurposingLogs($partnerId, $mediaRepurposingId, $startDate, $endDate);
		
		try
		{
			$runsLogs = $this->getData($partnerId, $mediaRepurposingId, $startDate, $endDate);
			/* @var $runsLogs array KalturaAuditTrail */
			
			$mediaRepurposingLogForm->populateLogData($runsLogs);
			$action->view->auditTrails = $runsLogs;
			$action->view->formValid = true;
		}
		catch(Exception $e)
		{
		    $action->view->formValid = false;
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
		}
		$action->view->form = $mediaRepurposingLogForm;
	}

	private function getData($partnerId, $mediaRepurposingId, $startDate, $endDate) {

		$auditTrailPlugin = MediaRepurposingUtils::getPluginByName('Kaltura_Client_Audit_Plugin');
		$filter = new Kaltura_Client_Audit_Type_AuditTrailFilter();
		$filter->auditObjectTypeEqual = Kaltura_Client_Audit_Enum_AuditTrailObjectType::SCHEDULE_TASK;
		$filter->partnerIdEqual = $partnerId;
		$filter->orderBy = '-created_at';

		if ($startDate)
			$filter->createdAtGreaterThanOrEqual = $startDate;
		if ($endDate)
			$filter->createdAtLessThanOrEqual = $endDate;
		if ($mediaRepurposingId)
			$filter->objectIdEqual = $mediaRepurposingId;

		$pager = new Kaltura_Client_Type_FilterPager();
		if (!($startDate && $endDate))
			$pager->pageSize = 20;

		$result = $auditTrailPlugin->auditTrail->listAction($filter, $pager);
		if ($result->totalCount)
			return $result->objects;
		return array();
	}

}

