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

	public function doAction(Zend_Controller_Action $action)
	{
		$request = $action->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		$partnerId = $this->_getParam('filter_input') ? $this->_getParam('filter_input') : $request->getParam('partnerId');

		$action->view->allowed = $this->isAllowedForPartner($partnerId);

		// init filter
		$ssoProfileFilter = new Kaltura_Client_Sso_Type_SsoFilter();
		$ssoProfileFilter->orderBy = "-createdAt";

		$client = Infra_ClientHelper::getClient();
		$ssoPluginClient = Kaltura_Client_Sso_Plugin::get($client);

		// get results and paginate
		$paginatorAdapter = new Infra_FilterPaginator($ssoPluginClient->sso, "listAction", $partnerId, $ssoProfileFilter);
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

		if ($partnerId)
			$createSsoProfileForm->getElement("newPartnerId")->setValue($partnerId);

		$action->view->newSsoProfileFolderForm = $createSsoProfileForm;
	}

	public function getInstance($interface)
	{
		if ($this instanceof $interface)
			return $this;

		return null;
	}

	public function isAllowedForPartner($partnerId)
	{
		$client = Infra_ClientHelper::getClient();
		$client->setPartnerId($partnerId);
		$filter = new Kaltura_Client_Type_PermissionFilter();
		$filter->nameEqual = Kaltura_Client_Enum_PermissionName::REACH_PLUGIN_PERMISSION;
		$filter->partnerIdEqual = $partnerId;
		try
		{
			$result = $client->permission->listAction($filter, null);
		} catch (Exception $e)
		{
			$client->setPartnerId(self::ADMIN_CONSOLE_PARTNER);
			return false;
		}
		$client->setPartnerId(self::ADMIN_CONSOLE_PARTNER);

		$isAllowed = ($result->totalCount > 0) && ($result->objects[0]->status == Kaltura_Client_Enum_PermissionStatus::ACTIVE);
		return $isAllowed;
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