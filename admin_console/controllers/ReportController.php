<?php
/**
 * @package Admin
 * @subpackage Reports
 */
class ReportController extends Zend_Controller_Action
{
	public function listAction()
	{
		$request = $this->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		
		$action = $this->view->url(array('controller' => $request->getControllerName(), 'action' => $request->getActionName()), null, true);

		$newButton = new Form_NewButton();
		$newButton->populate($request->getParams());
		$form = new Form_ReportFilter();
		$form->setAction($action);
		$form->populate($request->getParams());
		
		$reportFilter = $this->getReportFilterFromRequest($request);
		$reportFilter->orderBy = Kaltura_Client_Enum_ReportOrderBy::CREATED_AT_DESC;
		
		$newButton->getElement('newPartnerId')->setValue($reportFilter->partnerIdIn);
		// get results and paginate
		$client = Infra_ClientHelper::getClient();
		$adminConsolePlugin = Kaltura_Client_AdminConsole_Plugin::get($client);
		$paginatorAdapter = new Kaltura_FilterPaginatorWithPartnerLoader($adminConsolePlugin->reportAdmin, "listAction", null, $reportFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		
		// set view
		$this->view->form = $form;
		$this->view->newButton = $newButton;
		$this->view->paginator = $paginator;
	}
	
	public function createAction()
	{
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest();
		$action = $this->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')));
		$form = new Form_Report();
		$form->setAction($action);
		$client = Infra_ClientHelper::getClient();
		$adminConsolePlugin = Kaltura_Client_AdminConsole_Plugin::get($client);
		
		if ($request->isPost())
		{
			if ($form->isValid($request->getParams()))
			{
				$report = $form->getObject('Kaltura_Client_Type_Report', $request->getPost());
				$report = $adminConsolePlugin->reportAdmin->add($report);
				$form->setAttrib('class', 'valid');
				$this->view->formValid = true;
			}
		} 
		else 
		{
				$form->getElement('partner_id')->setAttrib('readonly',true);
		}
		$form->populate($request->getParams());
		$this->view->form = $form;
		$this->_helper->viewRenderer('edit'); 
	}
	
	public function editAction()
	{
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest();
		$id = $request->getParam('id');
		$action = $this->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')));
		$form = new Form_Report();
		$form->setAction($action);
		
		$client = Infra_ClientHelper::getClient();
		$adminConsolePlugin = Kaltura_Client_AdminConsole_Plugin::get($client);
		
		$report = $adminConsolePlugin->reportAdmin->get($id);

		if ($request->isPost())
		{
			if ($form->isValid($request->getParams()))
			{
				$reportUpdate = $form->getObject('Kaltura_Client_Type_Report', $request->getPost());
				$report = $adminConsolePlugin->reportAdmin->update($id, $reportUpdate);
				$form->populateFromObject($report);
				$form->setAttrib('class', 'valid');
				$this->view->formValid = true;
			}
			else
			{
				$form->populate($request->getParams());
			}
		}
		else
		{
			$form->populateFromObject($report);
		}
		$this->view->form = $form;
	}
	
	public function deleteAction() 
	{
		$this->_helper->viewRenderer->setNoRender();
		$request = $this->getRequest();
		$id = $request->getParam('id');
		$client = Infra_ClientHelper::getClient();
		$adminConsolePlugin = Kaltura_Client_AdminConsole_Plugin::get($client);
		
		$adminConsolePlugin->reportAdmin->delete($id);
		
		echo $this->_helper->json('ok', false);
	}
	
	protected function getReportFilterFromRequest(Zend_Controller_Request_Abstract $request)
	{
		$reportFilter = new Kaltura_Client_Type_ReportFilter();
		$partnerFilter = null;
		$filterType = $request->getParam('filter_type');
		$filterInput = $request->getParam('filter_input');
		switch($filterType)
		{
			case 'by-report-id':
				$reportFilter->idIn = $filterInput;
				break;
			case 'byid':
				$reportFilter->partnerIdIn = $filterInput;
				break;
			case 'by-partner-name':
				$partnerFilter = new Kaltura_Client_Type_PartnerFilter();
				$partnerFilter->nameLike = $filterInput;
				$statuses = array();
				$statuses[] = Kaltura_Client_Enum_PartnerStatus::ACTIVE;
				$statuses[] = Kaltura_Client_Enum_PartnerStatus::BLOCKED;
				$partnerFilter->statusIn = implode(',', $statuses);
				$partnerFilter->orderBy = Kaltura_Client_Enum_PartnerOrderBy::ID_DESC;
				$client = Infra_ClientHelper::getClient();
				$systemPartnerPlugin = Kaltura_Client_SystemPartner_Plugin::get($client);
				$partnersResponse = $systemPartnerPlugin->systemPartner->listAction($partnerFilter);
		
				if (count($partnersResponse->objects) == 0)
				{
					$reportFilter->idEqual = -1; // nothing should be found
				}
				else
				{
					$partnerIds = array();
					foreach($partnersResponse->objects as $partner)
						$partnerIds[] = $partner->id;
					$reportFilter->partnerIdIn = implode(',', $partnerIds);
				}
		}
		
		return $reportFilter;
	}
	
	public function testAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$request = $this->getRequest();
		$id = $request->getParam('id');
		$params = $request->getParam('params');
		$client = Infra_ClientHelper::getClient();
		$adminConsolePlugin = Kaltura_Client_AdminConsole_Plugin::get($client);
		
		$paramsArray = array();
		if (is_array($params))
		{
			foreach($params as $key => $value)
			{
				$keyValue = new Kaltura_Client_Type_KeyValue();
				$keyValue->key = $key;
				$keyValue->value = $value;
				$paramsArray[] = $keyValue;
			}
		}
		try
		{
			$response = $adminConsolePlugin->reportAdmin->executeDebug($id, $paramsArray);
		}
		catch(Exception $ex)
		{
			$response = array('code' => $ex->getCode(), 'message' => $ex->getMessage());
		}
		
		echo $this->_helper->json($response, false);
	}
	
	public function getParametersAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$request = $this->getRequest();
		$id = $request->getParam('id');
		$client = Infra_ClientHelper::getClient();
		$adminConsolePlugin = Kaltura_Client_AdminConsole_Plugin::get($client);
		
		try
		{
			$response = $adminConsolePlugin->reportAdmin->getParameters($id);
		}
		catch(Exception $ex)
		{
			$response = array('code' => $ex->getCode(), 'message' => $ex->getMessage());
		}
		
		echo $this->_helper->json($response, false);
	}
	
	public function getUrlAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$request = $this->getRequest();
		$id = $request->getParam('id');
		$partnerId = $request->getParam('partner-id');
		$client = Infra_ClientHelper::getClient();
		$adminConsolePlugin = Kaltura_Client_AdminConsole_Plugin::get($client);
		
		try
		{
			$url = $adminConsolePlugin->reportAdmin->getCsvUrl($id, $partnerId);
			$response = array('url' => $url);
		}
		catch(Exception $ex)
		{
			$response = array('code' => $ex->getCode(), 'message' => $ex->getMessage());
		}
		
		echo $this->_helper->json($response, false);
	}
}