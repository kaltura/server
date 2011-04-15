<?php
class WidgetController extends Zend_Controller_Action
{
	public function listAction()
	{
		$request = $this->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		
		$action = $this->view->url(array('controller' => $request->getControllerName(), 'action' => $request->getActionName()), null, true);

		$newButton = new Form_NewButton();
		
		$form = new Form_WidgetFilter();
		$form->setAction($action);
		$form->populate($request->getParams());
		
		$uiConfFilter = $this->getUiConfFilterFromRequest($request);
		$uiConfFilter->orderBy = Kaltura_Client_Enum_UiConfOrderBy::CREATED_AT_DESC;
		
		// get results and paginate
		$paginatorAdapter = new Infra_FilterPaginatorWithPartnerLoader(null, $uiConfFilter);
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
		$form = new Form_Widget();
		$form->setObjTypes($this->getSupportedUiConfTypes());
		$form->setAction($action);
		$client = Infra_ClientHelper::getClient();
		$adminConsolePlugin = Kaltura_Client_AdminConsole_Plugin::get($client);
		
		if ($request->isPost())
		{
			$form->loadVersions($request->getParam('obj_type'));
			if ($form->isValid($request->getParams()))
			{
				$uiConf = $form->getObject('Kaltura_Client_AdminConsole_Type_UiConfAdmin', $request->getPost());
				$uiConf = $adminConsolePlugin->uiConfAdmin->add($uiConf);
				$form->setAttrib('class', 'valid');
			}
		}
		$form->populate($request->getParams());
		$form->setEditorButtons();
		$this->view->typesInfo = $client->uiConf->getAvailableTypes();
		$this->view->form = $form;
		$this->_helper->viewRenderer('edit'); 
	}
	
	public function editAction()
	{
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest();
		$id = $request->getParam('id');
		$action = $this->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')));
		$form = new Form_Widget();
		$form->setObjTypes($this->getSupportedUiConfTypes());
		$form->setAction($action);
		
		$client = Infra_ClientHelper::getClient();
		$adminConsolePlugin = Kaltura_Client_AdminConsole_Plugin::get($client);
		
		$uiConf = $adminConsolePlugin->uiConfAdmin->get($id);

		if ($request->isPost())
		{
			$form->loadVersions($request->getParam('obj_type'));
			if ($form->isValid($request->getParams()))
			{
				$uiConfUpdate = $form->getObject('Kaltura_Client_AdminConsole_Type_UiConfAdmin', $request->getPost());
				$uiConf = $adminConsolePlugin->uiConfAdmin->update($id, $uiConfUpdate);
				$form->populateFromObject($uiConf);
				$form->setAttrib('class', 'valid');
			}
			else
			{
				$form->populate($request->getParams());
			}
		}
		else
		{
			$form->loadVersions($uiConf->objType);
			$form->populateFromObject($uiConf);
		}
		$form->setEditorButtons();
		$this->view->typesInfo = $client->uiConf->getAvailableTypes();
		$this->view->form = $form;
	}
	
	public function deleteAction() 
	{
		$this->_helper->viewRenderer->setNoRender();
		$request = $this->getRequest();
		$id = $request->getParam('id');
		$client = Infra_ClientHelper::getClient();
		$adminConsolePlugin = Kaltura_Client_AdminConsole_Plugin::get($client);
		
		$uiConf = $adminConsolePlugin->uiConfAdmin->delete($id);
		
		echo $this->_helper->json('ok', false);
	}
	
	public function duplicateAction() 
	{
		$this->_helper->viewRenderer->setNoRender();
		$request = $this->getRequest();
		$id = $request->getParam('id');
		$client = Infra_ClientHelper::getClient();
		$adminConsolePlugin = Kaltura_Client_AdminConsole_Plugin::get($client);
		
		$uiConf = $adminConsolePlugin->uiConfAdmin->get($id);
		$uiConf->id = null;
		$uiConf = $adminConsolePlugin->uiConfAdmin->add($uiConf);
		
		echo $this->_helper->json('ok', false);
	}
	
	public function kcwEditorAction()
	{
		$request = $this->getRequest();
		$this->view->kcwEditorVersion = "v1.2.0"; 
		$this->view->kcwBaseUrl = Infra_ClientHelper::getServiceUrl() . '/flash/kcweditor/';
		$this->_helper->layout->setLayout('layout_empty');
	}
	
	protected function getUiConfFilterFromRequest(Zend_Controller_Request_Abstract $request)
	{
		$uiConfFilter = new Kaltura_Client_Type_UiConfFilter();
		$uiConfFilter->objTypeIn = implode(',', array_keys($this->getSupportedUiConfTypes()));
		$partnerFilter = null;
		$filterType = $request->getParam('filter_type');
		$filterInput = $request->getParam('filter_input');
		switch($filterType)
		{
			case 'by-uiconf-id':
				$uiConfFilter->idIn = $filterInput;
				break;
			case 'by-partner-id':
				$uiConfFilter->partnerIdIn = $filterInput;
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
				$partnersResponse = $client->systemPartner->listAction($partnerFilter);
				if (count($partnersResponse->objects) == 0)
				{
					$uiConfFilter->idEqual = -1; // nothing should be found
				}
				else
				{
					$partnerIds = array();
					foreach($partnersResponse->objects as $partner)
						$partnerIds[] = $partner->id;
					$uiConfFilter->partnerIdIn = implode(',', $partnerIds);
				}
		}
		
		return $uiConfFilter;
	}
	
	protected function getSupportedUiConfTypes()
	{
		$types = array();
		$typesConfig = Zend_Registry::get('config')->settings->uiConfTypes;
		if ($typesConfig) 
		{
			foreach($typesConfig as $config)
			{
				if (is_string($config))
				{
					$value = eval('return ' . $config . ';');
					$types[$value] = $config;
				}
			}
		}
		return $types;
	}
}