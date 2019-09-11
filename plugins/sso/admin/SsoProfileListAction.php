<?php
/**
 * @package plugins.sso
 * @subpackage Admin
 */
class SsoProfileListAction extends KalturaApplicationPlugin implements IKalturaAdminConsolePublisherAction
{
	const ADMIN_CONSOLE_PARTNER = "-2";

	public function __construct()
	{
		$this->action = 'SsoProfileListAction';
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

	/**
	 * @param Zend_Controller_Action $action
	 * @throws Infra_Exception
	 */
	public function doAction(Zend_Controller_Action $action)
	{
		$request = $action->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);

		// init filter
		$ssoProfileFilter = $this->getSsoFilterFromRequest($request);
		$ssoProfileFilter->orderBy = "-createdAt";

		$client = Infra_ClientHelper::getClient();
		$ssoPluginClient = Kaltura_Client_Sso_Plugin::get($client);

		// get results and paginate
		$paginatorAdapter = new Infra_FilterPaginator($ssoPluginClient->sso, "listAction", null, $ssoProfileFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);

		// set view
		$ssoProfileFilterForm = new Form_SsoProfileFilter();
		$ssoProfileFilterForm->populate($request->getParams());
		$ssoProfileFilterFormAction = $action->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);
		$ssoProfileFilterForm->setAction($ssoProfileFilterFormAction);

		$action->view->filterForm = $ssoProfileFilterForm;
		$action->view->paginator = $paginator;

		$createSsoProfileForm = new Form_CreateSsoProfile();
		$actionUrl = $action->view->url(array('controller' => 'plugin', 'action' => 'SsoProfileConfigure'), null, true);
		$createSsoProfileForm->setAction($actionUrl);

		if($ssoProfileFilter && isset($ssoProfileFilter->partnerIdEqual))
			$createSsoProfileForm->getElement("newPartnerId")->setValue($ssoProfileFilter->partnerIdEqual);

		$action->view->newSsoProfileFolderForm = $createSsoProfileForm;
	}

	/**
	 * @param Zend_Controller_Request_Abstract $request
	 * @return Kaltura_Client_Sso_Type_SsoFilter
	 */
	private function getSsoFilterFromRequest(Zend_Controller_Request_Abstract $request)
	{
		$filter = new Kaltura_Client_Sso_Type_SsoFilter();
		$filterInput = $request->getParam('filter_input');
		if(!strlen($filterInput))
			return $filter;

		$filterType = $request->getParam('filter_type');
		$filter->$filterType = $filterInput;

		return $filter;
	}

	public function getInstance($interface)
	{
		if ($this instanceof $interface)
			return $this;

		return null;
	}


	/**
	 * @return array<string, string> - array of <label, jsActionFunctionName>
	 */
	public function getPublisherAdminActionOptions($partner, $permissions)
	{
		$options = array ();
		$options [] = array (0 => 'Sso', 1 => 'listSsoProfiles' );
		return $options;
	}

	/**
	 * @return string javascript code to add to publisher list view
	 */
	public function getPublisherAdminActionJavascript()
	{
		$functionStr = 'function listSsoProfiles(partnerId) {
			var url = pluginControllerUrl + \'/' . get_class ( $this ) . '/filter_type/partnerIdEqual/filter_input/\' + partnerId;
			document.location = url;
		}';
		return $functionStr;
	}
}