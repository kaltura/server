<?php
/**
 * @package Admin
 */
class IndexController extends Zend_Controller_Action
{
    public function init()
    {
    }

    public function indexAction()
    {
    	if (Infra_AclHelper::isAllowed('partner', 'list'))
        	$this->_helper->redirector('list', 'partner');
    }
    
    public function loginAction()
    {
    	
    }
    
    public function testmeAction()
    {
    	
    }
    
    public function testmeDocAction()
    {
    	
    }
    
    public function apcAction()
    {
    	
    }
	    
    public function memcacheAction()
    {
    	
    }
	
	public function clientLibsAction()
    {
    	
    }
    
	public function xsdDocAction()
    {
    	
    }
	
	public function kavaAction()
	{
		$settings = Zend_Registry::get('config')->settings;
		if (!isset($settings->kavaDashboard))
		{
			return;
		}
		
		$kavaDashboard = $settings->kavaDashboard;

		$this->view->kavaDashboardUrl = rtrim($kavaDashboard->url, "/") . "/?jwt=" . 
			Form_JwtHelper::getJwt(
				$kavaDashboard->jwtKey, 
				$settings->partnerId, 
				$settings->sessionExpiry);
	}

	public function kelloggsAction()
	{
		$settings = Zend_Registry::get('config')->settings;
		if(!isset($settings->kelloggsDashboard))
		{
			return;
		}

		if (!Infra_AclHelper::isAllowed('developer', 'kelloggs'))
		{
			return;
		}

		$kelloggsDashboard = $settings->kelloggsDashboard;
		$this->view->kelloggsUrl = $kelloggsDashboard->url;
		$this->view->kelloggsServiceUrl = $kelloggsDashboard->serviceUrl;
		$this->view->kelloggsJwt = Form_JwtHelper::getJwt(
			$kelloggsDashboard->jwtKey, 
			$settings->partnerId, 
			$settings->sessionExpiry);
	}

	public function entryRestorationAction()
	{
		$request = $this->getRequest();
		$this->view->errors = array();

		$action = $this->view->url(array('controller' => 'index', 'action' => 'entry-restoration'), null, true);

		$this->view->entryRestorationForm = new Form_EntryRestoration();
		$this->view->entryRestorationForm->populate($request->getParams());
		$this->view->entryRestorationForm->setAction($action);

		// Handle file upload to extract entry IDs
		if ($request->isPost()) {
			$upload = new Zend_File_Transfer_Adapter_Http();
			$files = $upload->getFileInfo();
			if (count($files) && isset($files['entryFile']) && $files['entryFile']['size']) {
				$file = $files['entryFile'];
				$entryIds = file_get_contents($file['tmp_name']);

				$entryIdsField = $this->view->entryRestorationForm->getElement('entryIds');
				$entryIdsField->setValue($entryIds);
			}
		}
	}

	public function restoreEntryAjaxAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$request = $this->getRequest();
		$entryId = $request->getParam('entryId');

		header('Content-Type: application/json');

		if (!$entryId) {
			echo json_encode(array(
				'success' => false,
				'status' => 'Failed',
				'message' => 'Entry ID is required',
				'type' => 'error'
			));
			return;
		}

		try {
			// Unimpersonate to run as admin console user (like existing restore in BatchController)
			Infra_ClientHelper::unimpersonate();
			$client = Infra_ClientHelper::getClient();
			$adminConsolePlugin = Kaltura_Client_AdminConsole_Plugin::get($client);
			$result = $adminConsolePlugin->entryAdmin->restoreDeletedEntry($entryId);

			echo json_encode(array(
				'success' => true,
				'status' => 'Restored',
				'message' => 'Successfully restored',
				'type' => 'success'
			));
		} catch (Exception $e) {
			$errorMsg = $e->getMessage();
			$errorCode = $e->getCode();
			$status = 'Failed';
			$type = 'error';

			// Categorize error types
			if (strpos($errorCode, 'ENTRY_ASSETS_WRONG_STATUS_FOR_RESTORE') !== false ||
				stripos($errorMsg, 'wrong status') !== false ||
				stripos($errorMsg, 'cannot be restored') !== false ||
				stripos($errorMsg, 'not deleted') !== false ||
				strpos($errorCode, 'ENTRY_ID_NOT_FOUND') !== false ||
				stripos($errorMsg, 'not found') !== false) {
				$status = 'Not Restorable';
				$type = 'not_restorable';
			}

			echo json_encode(array(
				'success' => false,
				'status' => $status,
				'message' => $errorMsg,
				'type' => $type
			));
		}
	}
}