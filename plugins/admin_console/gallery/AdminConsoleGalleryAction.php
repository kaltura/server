<?php
/**
 * @package plugins.adminConsoleGallery
 * @subpackage admin
 */
class AdminConsoleGalleryAction extends KalturaApplicationPlugin
{
	public function __construct()
	{
		$this->action = 'AdminConsoleGalleryAction';
		$this->label = 'Gallery';
		$this->rootLabel = 'Batch Process Control';
	}
	
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	public function getRequiredPermissions()
	{
		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_ENTRY_INVESTIGATION);
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
		$request = $action->getRequest();

		$action->view->errors = array();
		
		$formAction = $action->view->url(array('controller' => 'plugin', 'action' => 'AdminConsoleGalleryAction'), null, true);
				
		$action->view->searchEntriesForm = new Form_Batch_SearchEntries();
        $action->view->searchEntriesForm->setAction($formAction);
		
        $filter = $action->view->searchEntriesForm->getFilter($request->getParams());
		$action->view->searchEntriesForm->populate($request->getParams());
	
		$client = Infra_ClientHelper::getClient();
		if(!$client)
		{
			$action->view->errors[] = 'init client failed';
			return;
		}
		
		$partnerId = $request->getParam('partnerId');
		if($partnerId > 0)
		{
			$paginatorAdapter = new Infra_FilterPaginator($client->media, "listAction", $partnerId, $filter);
			$paginator = new Infra_Paginator($paginatorAdapter, $request, null, 30);
			$paginator->setAvailablePageSizes(array(15, 30, 60, 100));
			$paginator->setAction($formAction);
			$action->view->paginator = $paginator;
			$action->view->playerPartnerId = $partnerId;
			$action->view->uiConf = null;
			$action->view->swfUrl = null;
			
			$adminConsolePlugin = Kaltura_Client_AdminConsole_Plugin::get($client);
			
			$uiConfId = Zend_Registry::get('config')->settings->defaultUiConfId;
			if($uiConfId)
			{
				$action->view->uiConf = $adminConsolePlugin->uiConfAdmin->get($uiConfId);
			}
			else
			{
				$uiConfFilter = new Kaltura_Client_Type_UiConfFilter();
				$uiConfFilter->partnerIdIn = 0;
				$uiConfFilter->objTypeEqual = Kaltura_Client_Enum_UiConfObjType::PLAYER_V3;
				$uiConfFilter->orderBy = Kaltura_Client_Enum_UiConfOrderBy::CREATED_AT_DESC;
				$uiConfPager = new Kaltura_Client_Type_FilterPager();
				$uiConfPager->pageSize = 1;
				$uiConfList = $adminConsolePlugin->uiConfAdmin->listAction($uiConfFilter, $uiConfPager);
				/* @var $uiConfList Kaltura_Client_AdminConsole_Type_UiConfAdminListResponse */
				if(count($uiConfList->objects))
					$action->view->uiConf = reset($uiConfList->objects);
			}
			
			if($action->view->uiConf)
				$action->view->swfUrl = "/index.php/kwidget/wid/_{$partnerId}/cache_st/" . time() . "/uiconf_id/" . $action->view->uiConf->id;
		}
	}
}

