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
		$uiConfFilter->orderBy = KalturaUiConfOrderBy::CREATED_AT_DESC;
		
		// get results and paginate
		$paginatorAdapter = new Kaltura_FilterPaginatorWithPartnerLoader(null, $uiConfFilter);
		$paginator = new Kaltura_Paginator($paginatorAdapter, $request);
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
		$client = Kaltura_ClientHelper::getClient();
		
		if ($request->isPost())
		{
			$form->loadVersions($request->getParam('obj_type'));
			if ($form->isValid($request->getParams()))
			{
				$uiConf = $form->getObject('KalturaUiConf', $request->getPost());
				$uiConf = $client->uiConfAdmin->add($uiConf);
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
		
		$client = Kaltura_ClientHelper::getClient();
		$uiConf = $client->uiConfAdmin->get($id);

		if ($request->isPost())
		{
			$form->loadVersions($request->getParam('obj_type'));
			if ($form->isValid($request->getParams()))
			{
				$uiConfUpdate = $form->getObject('KalturaUiConf', $request->getPost());
				$uiConf = $client->uiConfAdmin->update($id, $uiConfUpdate);
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
		$client = Kaltura_ClientHelper::getClient();
		
		$uiConf = $client->uiConfAdmin->delete($id);
		
		echo $this->_helper->json('ok', false);
	}
	
	public function duplicateAction() 
	{
		$this->_helper->viewRenderer->setNoRender();
		$request = $this->getRequest();
		$id = $request->getParam('id');
		$client = Kaltura_ClientHelper::getClient();
		
		$uiConf = $client->uiConfAdmin->get($id);
		$uiConf->id = null;
		$uiConf = $client->uiConfAdmin->add($uiConf);
		
		echo $this->_helper->json('ok', false);
	}
	
	public function kcwEditorAction()
	{
		$request = $this->getRequest();
		$this->view->kcwEditorVersion = "v1.2.0"; 
		$this->view->kcwBaseUrl = Kaltura_ClientHelper::getServiceUrl() . '/flash/kcweditor/';
		$this->_helper->layout->setLayout('layout_empty');
	}
	
	protected function getUiConfFilterFromRequest(Zend_Controller_Request_Abstract $request)
	{
		$uiConfFilter = new KalturaUiConfFilter();
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
				$partnerFilter = new KalturaPartnerFilter();
				$partnerFilter->nameLike = $filterInput;
				$statuses = array();
				$statuses[] = KalturaPartnerStatus::ACTIVE;
				$statuses[] = KalturaPartnerStatus::BLOCKED;
				$partnerFilter->statusIn = implode(',', $statuses);
				$partnerFilter->orderBy = KalturaPartnerOrderBy::ID_DESC;
				$client = Kaltura_ClientHelper::getClient();
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