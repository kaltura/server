<?php

/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class MediaRepurposingListAction extends KalturaApplicationPlugin implements IKalturaAdminConsolePublisherAction
{
	public function __construct()
	{
		$this->action = 'MediaRepurposingListAction';
		$this->label = null;
		$this->rootLabel = null;
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
		$partnerId = $this->_getParam('partnerId');
		
		$mrId = null;
		$filterType = $request->getParam('filter_type');
		if ($filterType == 'idEqual')
			$mrId = $request->getParam('filter_input');
		if ($filterType == 'partnerIdEqual')
			$partnerId = $request->getParam('filter_input');
		
		if (!$partnerId)
			$partnerId = 0;

		$action->view->allowed = MediaRepurposingUtils::isAllowMrToPartner($partnerId);
		
		$paginatorAdapter = new Kaltura_FilterPaginatorForMediaRepurposing($partnerId, $mrId);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);

		// set view
		$mediaRepurposingFilterForm = new Form_MediaRepurposingFilter();
		$mediaRepurposingFilterForm->partnerId = $partnerId;
		$mediaRepurposingFilterForm->populate($request->getParams());
		$mediaRepurposingFilterFormAction = $action->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);
		$mediaRepurposingFilterForm->setAction($mediaRepurposingFilterFormAction);

		$action->view->filterForm = $mediaRepurposingFilterForm;
		$action->view->paginator = $paginator;

		$actionUrl = $action->view->url(array('controller' => 'plugin', 'action' => 'MediaRepurposingConfigure'), null, true);


		$createMediaRepurposingForm = new Form_CreateMediaRepurposing();
		$createMediaRepurposingForm->setAction($actionUrl);
		$action->view->newMediaRepurposingForm = $createMediaRepurposingForm;

		$createMediaRepurposingFormFromTemplate = new Form_CreateMediaRepurposingFromTemplate();
		$createMediaRepurposingFormFromTemplate->setAction($actionUrl);
		$action->view->newMediaRepurposingFormFromTemplate = $createMediaRepurposingFormFromTemplate;

		$action->view->getDryRunLogForm = new Form_GetDryRunLog();
	}


	/**
	 * @return array<string, string> - array of <label, jsActionFunctionName>
	 */
	public function getPublisherAdminActionOptions($partner, $permissions)
	{
		$options = array();
		$options[] = array(0 => 'Media Repurposing', 1 => 'listMediaRepurposing');
		return $options;
	}

	/**
	 * @return string javascript code to add to publisher list view
	 */
	public function getPublisherAdminActionJavascript()
	{
		$functionStr = 'function listMediaRepurposing(partnerId) {
			var url = pluginControllerUrl + \'/' . get_class($this) . '/filter_type/partnerIdEqual/filter_input/\' + partnerId;
			document.location = url;
		}';
		return $functionStr;
	}

	public function getInstance($interface)
	{
		if ($this instanceof $interface)
			return $this;

		return null;
	}

}

