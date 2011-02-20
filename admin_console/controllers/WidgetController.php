<?php
class WidgetController extends Zend_Controller_Action
{
	public function listAction()
	{
		$request = $this->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		
		$action = $this->view->url(array('controller' => $request->getControllerName(), 'action' => $request->getActionName()), null, true);

		$form = new Form_WidgetFilter();
		$form->setAction($action);
		$form->populate($request->getParams());
		
		$uiConfFilter = $this->getUiConfFilterFromRequest($request);
		$uiConfFilter->orderBy = KalturaUiConfOrderBy::CREATED_AT_DESC;
		
		// get results and paginate
		$paginatorAdapter = new Kaltura_FilterPaginator("uiConf", "listAction", null, $uiConfFilter);
		$paginator = new Kaltura_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		
		// set view
		$this->view->form = $form;
		$this->view->paginator = $paginator;
	}
	
	public function createAction()
	{
		$request = $this->getRequest();
		$form = new Form_Widget();
		$client = Kaltura_ClientHelper::getClient();
		
		$partnerId = $request->getParam('partner_id');
		
		if (!$partnerId)
		{
			$this->_forward('selector', 'partner');
		}
		else
		{
			if ($request->isPost())
			{
				$form->loadVersions($request->getParam('obj_type'));
				if ($form->isValid($request->getParams()))
				{
					$uiConf = $form->getObject('KalturaUiConf', $request->getPost());
					$uiConf->partnerId = $partnerId;
					Kaltura_ClientHelper::impersonate($partnerId);
					$uiConf = $client->uiConf->add($uiConf);
					Kaltura_ClientHelper::unimpersonate();
					
					$this->_helper->redirector('list', 'widget', null, array(
						'filter_type' => 'by-partner-id',
						'filter_input' => $partnerId,
					));
				}
				else
				{
					$form->populate($request->getParams());
				}
			}
			$form->setEditorButtons();
			$this->view->typesInfo = $client->uiConf->getAvailableTypes();
			$this->view->form = $form;
			$this->_helper->viewRenderer('edit'); 
		}
	}
	
	public function editAction()
	{
		$request = $this->getRequest();
		$id = $request->getParam('id');
		$form = new Form_Widget();
		$form->setEditMode();
		
		$client = Kaltura_ClientHelper::getClient();
		$uiConf = $this->getUiConf($id);
		if (is_null($uiConf))
		{
			$this->view->notFound = true;
		}
		else 
		{
			if ($request->isPost())
			{
				$form->loadVersions($request->getParam('obj_type'));
				if ($form->isValid($request->getParams()))
				{
					$uiConfUpdate = $form->getObject('KalturaUiConf', $request->getPost());
					Kaltura_ClientHelper::impersonate($uiConf->partnerId);
					$uiConf = $client->uiConf->update($id, $uiConfUpdate);
					Kaltura_ClientHelper::unimpersonate();
					$form->populateFromObject($uiConf);
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
		}
		$this->view->typesInfo = $client->uiConf->getAvailableTypes();
		$this->view->form = $form;
	}
	
	public function kcwEditorAction()
	{
		$request = $this->getRequest();
		$this->view->kcwEditorVersion = "v1.2.0"; 
		$this->view->kcwBaseUrl = Kaltura_ClientHelper::getServiceUrl() . '/flash/kcweditor/';
		$this->_helper->layout->setLayout('layout_empty');
	}
	
	protected function getUiConf($id)
	{
		$client = Kaltura_ClientHelper::getClient();
		$filter = new KalturaUiConfFilter();
		$filter->idEqual = $id;
		// use uiconf.list because we don't have access to private uiconfs using uiconf.get
		// and we don't know the partner id of this uiconf to impersonate
		$uiConfResult = $client->uiConf->listAction($filter);
		
		return (count($uiConfResult->objects) >= 1 ? $uiConfResult->objects[0] : null);
	}
	
	protected function getUiConfFilterFromRequest(Zend_Controller_Request_Abstract $request)
	{
		$uiConfFilter = new KalturaUiConfFilter();
		$partnerFilter = null;
		$filterType = $request->getParam('filter_type');
		$filterInput = $request->getParam('filter_input');
		switch($filterType)
		{
			case 'by-partner-id':
				$uiConfFilter->partnerIdIn = $filterInput;
				break;
			case 'by-uiconf-id':
				$uiConfFilter->idIn = $filterInput;
				break;
		}
		
		return $uiConfFilter;
	}
}