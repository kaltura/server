<?php
/**
 * @package plugins.eventNotification
 * @subpackage admin
 */
class EventNotificationTemplatesListAction extends KalturaApplicationPlugin implements IKalturaAdminConsolePublisherAction
{
	public function __construct()
	{
		$this->action = 'listEventNotificationTemplates';
		$this->label = null;
		$this->rootLabel = null;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaApplicationPlugin::getTemplatePath()
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	/* (non-PHPdoc)
	 * @see KalturaApplicationPlugin::getRequiredPermissions()
	 */
	public function getRequiredPermissions()
	{
		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_EVENT_NOTIFICATION_BASE);
	}
	
	/**
	 * @param Zend_Controller_Request_Abstract $request
	 * @return Kaltura_Client_EventNotification_Type_EventNotificationTemplateFilter
	 */
	protected function getEventNotificationTemplatesFilterFromRequest(Zend_Controller_Request_Abstract $request)
	{
		$filter = new Kaltura_Client_EventNotification_Type_EventNotificationTemplateFilter();
		$filterInput = $request->getParam('filter_input');
		if(!strlen($filterInput))
			return $filter;
		
		$filterType = $request->getParam('filter_type');
		$filter->$filterType = $filterInput;
		
		return $filter;
	}
	
	/**
	 * @param Zend_Controller_Request_Abstract $request
	 * @return Kaltura_Client_Type_PartnerFilter
	 */
	private function getPartnerFilterFromRequest(Zend_Controller_Request_Abstract $request)
	{
		$filterInput = $request->getParam('filter_input');
		if(!strlen($filterInput))
			return null;
			
		$filter = new Kaltura_Client_Type_PartnerFilter();
		$filterType = $request->getParam('filter_type');
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
		return $filter;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaApplicationPlugin::doAction()
	 */
	public function doAction(Zend_Controller_Action $action)
	{
		$request = $action->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		
		$form = new Form_EventNotificationTemplatesFilter();
		$form->populate($request->getParams());
		$actionUrl = $action->view->url(array('controller' => 'plugin', 'action' => 'EventNotificationTemplatesListAction'), null, true);
		$form->setAction($actionUrl);
		
		// init filter
		$eventNotificationTemplatesFilter = $this->getEventNotificationTemplatesFilterFromRequest($request);
		
		$client = Infra_ClientHelper::getClient();
		$eventNotificationPlugin = Kaltura_Client_EventNotification_Plugin::get($client);
		
		// get results and paginate
		$paginatorAdapter = new Infra_FilterPaginator($eventNotificationPlugin->eventNotificationTemplate, "listAction", null, $eventNotificationTemplatesFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		
		$newForm = new Form_NewEventNotificationTemplate();
		if ($eventNotificationTemplatesFilter && isset($eventNotificationTemplatesFilter->partnerIdEqual))
		{
			$newForm->getElement('newPartnerId')->setValue($eventNotificationTemplatesFilter->partnerIdEqual);
		}
		
		$listTemplatesPager = new Kaltura_Client_Type_FilterPager();
		$listTemplatesPager->pageSize = 500;
		$templatesList = $eventNotificationPlugin->eventNotificationTemplate->listTemplates(null, $listTemplatesPager);
		
		$templates = array();
		foreach($templatesList->objects as $template)
		{
			$obj = new stdClass();
			$obj->id = $template->id;
			$obj->type = $template->type;
			$obj->name = $template->name;
			$templates[] = $obj;
		}
			
		// set view
		$action->view->form = $form;
		$action->view->newForm = $newForm;
		$action->view->paginator = $paginator;
		$action->view->templates = $templates;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaAdminConsolePublisherAction::getPublisherAdminActionOptions()
	 */
	public function getPublisherAdminActionOptions($partner, $permissions)
	{
		$options = array();
		$options[] = array (0 => 'Event Notifications', 1 => 'eventNotificationTemplates');
		return $options;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaAdminConsolePublisherAction::getPublisherAdminActionJavascript()
	 */
	public function getPublisherAdminActionJavascript()
	{
		$functionStr = 'function eventNotificationTemplates(partnerId) {
			var url = pluginControllerUrl + /'.get_class($this).'/ + \'filter_type/partnerIdEqual/filter_input/\' + partnerId;
			document.location = url;
		}';
		return $functionStr;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaBase::getInstance()
	 */
	public function getInstance($interface)
	{
		if($this instanceof $interface)
			return $this;
			
		return null;
	}
}

