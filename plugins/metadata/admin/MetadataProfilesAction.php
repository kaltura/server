<?php

class MetadataProfilesAction extends KalturaAdminConsolePlugin
{
	public function __construct($label = null, $action = null, $rootLabel = null)
	{
		$this->action = $action;
		$this->label = $label;
		$this->rootLabel = $rootLabel;
	}
	
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	public function getRole()
	{
		return Kaltura_AclHelper::ROLE_PROFESIONAL_SERVICES;
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
		
		$request = $action->getRequest();
		$page = $request->getParam('page', 1);
		
		$pageSize = $request->getParam('pageSize', 10);
		
		$action->view->form = new Form_PartnerFilter();
		
		// init filter
		$partnerFilter = $this->getPartnerFilterFromRequest($request);
		/*
		// get results and paginate
		$paginatorAdapter = new Kaltura_FilterPaginator("metadataProfile", "listAction", $partnerFilter);
		$paginator = new Kaltura_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		
		// popule the form
		$form->populate($request->getParams());
		
		// set view
		$action->view->form = $form;
		$action->view->paginator = $paginator;
		
		
			*/
		
		return;
		$request = $action->getRequest();		
		
		$action->view->metadataProfilesForm = new Form_metadataProfiles();
		$action->view->metadataProfilesForm->populate($request->getParams());
		
		$partnerId = $request->getParam('partnerId', false);
		$entryId = $request->getParam('entryId', false);
		$freeText = $request->getParam('freeText', false);
		
		$form = new Form_PartnerFilter();
		$form->populate($request->getParams());
		$action->view->form = $form;

		if(($partnerId) || ($entryId) || ($freeText))
		{
			try
			{
				$client = Kaltura_ClientHelper::getClient();
				$metadataProfileFilter = new KalturaMetadataProfileFilter();
				$metadataProfileFilter->partnerIdEqual = $partnerId;
				$this->view->inQueuePaginator = null;
				
				//$client->systemUser->add($systemUser);
				
			}
			catch(Exception $ex)
			{
				//to do
			}
		}	
	}	
	
	private function getPartnerFilterFromRequest(Zend_Controller_Request_Abstract $request)
	{
		$filter = new KalturaMetadataProfileFilter();
		$filterType = $request->getParam('filter_type');
		$filterInput = $request->getParam('filter_input');
		if ($filterType == 'byid')
		{
			$filter->idEqual = $filterInput;
		}
		else
		{
			if ($filterType == 'byname')
				$filter->nameLike = $filterInput;
			//elseif ($filterType == 'free' && $filterInput)
				//$filter->
		}
		$statuses = array();
		$statuses[] = KalturaPartnerStatus::ACTIVE;
		$statuses[] = KalturaPartnerStatus::BLOCKED;
		$filter->statusIn = implode(',', $statuses);
		$filter->orderBy = KalturaPartnerOrderBy::ID_DESC;
		return $filter;
	}
}