<?php
/**
 * @package plugins.audit
 * @subpackage Admin
 */
class AuditTrailListAction extends KalturaApplicationPlugin
{

	public function __construct()
	{
		$this->action = 'ConfigurationMapListAction';
		$this->label = "Audit Trail";
		$this->rootLabel = "Configuration";
	}

	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}

	public function doAction(Zend_Controller_Action $action)
	{
		$request = $action->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		$partnerId = null;

		$client = Infra_ClientHelper::getClient();
		$auditTrailClient = Kaltura_Client_Audit_Plugin::get($client);

		$auditTrailFilter = $this->getAuditTrailFilter($request);
		$auditTrailFilter->orderBy = "-createdAt";

		if ($request->getParam('filter_type') && $request->getParam('filter_input') && $request->getParam('filter_object_id'))
		{
			$paginator = self::getPaginator($auditTrailClient, $auditTrailFilter, $request, $page, $pageSize, $partnerId);
			$action->view->paginator = $paginator;
		}

		$auditTrailFilterForm = self::getFilterForm($request, $action);
		$action->view->filterForm = $auditTrailFilterForm;
	}

	protected function getAuditTrailFilter(Zend_Controller_Request_Abstract $request)
	{
		$filter = new Kaltura_Client_Audit_Type_AuditTrailFilter();
		$auditTrailObjectType = $request->getParam('filter_input');
		if(!$auditTrailObjectType)
		{
			return $filter;
		}
		$filterType = $request->getParam('filter_type');
		$filter->$filterType = $auditTrailObjectType;
		$auditTrailObjectId = $request->getParam('filter_object_id');
		if (strlen($auditTrailObjectId))
		{
			$filter->objectIdEqual  = $auditTrailObjectId;
		}
		return $filter;
	}

	protected static function getPaginator($auditTrailClient, $auditTrailFilter, $request, $page, $pageSize, $partnerId)
	{
		// get results and paginate
		$paginatorAdapter = new Infra_FilterPaginator($auditTrailClient->auditTrail, 'listAction', $partnerId, $auditTrailFilter);

		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		return $paginator;
	}

	protected static function getFilterForm($request, $action)
	{
		// set view
		$auditTrailFilterForm = new Form_AuditTrailFilter();
		$auditTrailFilterForm->populate($request->getParams());
		$auditTrailFilterFormAction = $action->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);
		$auditTrailFilterForm->setAction($auditTrailFilterFormAction);
		return $auditTrailFilterForm;
	}
}