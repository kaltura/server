<?php
class PartnerListAction extends KalturaAdminConsolePlugin
{
	
	public function __construct()
	{
		$this->action = 'listNoneCommercial';
		$this->label = 'None Commercial';
		$this->rootLabel = 'Publishers';
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
	
	private function getPartnerFilterFromRequest(Zend_Controller_Request_Abstract $request)
	{
		$filter = new KalturaProfesionalServicesPartnerFilter();
		$filterType = $request->getParam('filter_type');
		$filterInput = $request->getParam('filter_input');
		if ($filterType == 'byid')
		{
			$filter->idIn = $filterInput;
		}
		else
		{
			if ($filterType == 'byname')
				$filter->nameLike = $filterInput;
			elseif ($filterType == 'free' && $filterInput)
				$filter->partnerNameDescriptionWebsiteAdminNameAdminEmailLike = $filterInput;
		}
		$statuses = array();
		$statuses[] = KalturaPartnerStatus::ACTIVE;
		$statuses[] = KalturaPartnerStatus::BLOCKED;
		$filter->statusIn = implode(',', $statuses);
		$filter->orderBy = KalturaPartnerOrderBy::ID_DESC;
		$filter->commercialUseEqual = KalturaCommercialUseType::NON_COMMERCIAL_USE;
		return $filter;
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
		$request = $action->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		
		$actionUrl = $action->view->url(array('controller' => 'plugin', 'action' => 'PartnerListAction'), null, true);

		$client = Kaltura_ClientHelper::getClient();
		
		$form = new Form_PartnerFilter();
		$form->setAction($actionUrl);
		
		// init filter
		$partnerFilter = $this->getPartnerFilterFromRequest($request);
		
		// get results and paginate
		$paginatorAdapter = new Kaltura_FilterPaginator("systemPartner", "listAction", $partnerFilter);
		$paginator = new Kaltura_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		
		// popule the form
		$form->populate($request->getParams());
		
		// set view
		$action->view->form = $form;
		$action->view->paginator = $paginator;
	}
}

