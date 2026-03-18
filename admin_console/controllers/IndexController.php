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

				// Validate file extension immediately
				$fileName = $file['name'];
				$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
				$allowedExtensions = array('txt', 'csv');

				if (!in_array($fileExtension, $allowedExtensions)) {
					$this->view->errors[] = "Invalid file type. Only .txt and .csv files are accepted. You uploaded: .$fileExtension";
					return;
				}

				// Validate file size (5MB max)
				$maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
				if ($file['size'] > $maxFileSize) {
					$this->view->errors[] = "File is too large. Maximum file size is 5MB.";
					return;
				}

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

		// Only accept POST requests for restore operations
		if (!$request->isPost()) {
			echo $this->_helper->json(array(
				'success' => false,
				'status' => 'Failed',
				'message' => 'Only POST requests are allowed',
				'type' => 'error'
			), false);
			return;
		}

		$entryId = $request->getParam('entryId');

		if (!$entryId) {
			echo $this->_helper->json(array(
				'success' => false,
				'status' => 'Failed',
				'message' => 'Entry ID is required',
				'type' => 'error'
			), false);
			return;
		}

		try {
			// Unimpersonate to run as admin console user (like existing restore in BatchController)
			Infra_ClientHelper::unimpersonate();
			$client = Infra_ClientHelper::getClient();
			$adminConsolePlugin = Kaltura_Client_AdminConsole_Plugin::get($client);
			$result = $adminConsolePlugin->entryAdmin->restoreDeletedEntry($entryId);

			echo $this->_helper->json(array(
				'success' => true,
				'status' => 'Restored',
				'message' => 'Successfully restored',
				'type' => 'success'
			), false);
		} catch (Exception $e) {
			$errorMsg = $e->getMessage();
			$errorCode = (string)$e->getCode();
			$status = 'Failed';
			$type = 'error';

			// Categorize error types
			// Check message for error indicators since errorCode is typically numeric
			if (stripos($errorMsg, 'ENTRY_ASSETS_WRONG_STATUS_FOR_RESTORE') !== false ||
				stripos($errorMsg, 'wrong status') !== false ||
				stripos($errorMsg, 'cannot be restored') !== false ||
				stripos($errorMsg, 'not deleted') !== false ||
				stripos($errorMsg, 'ENTRY_ID_NOT_FOUND') !== false ||
				stripos($errorMsg, 'not found') !== false) {
				$status = 'Not Restorable';
				$type = 'not_restorable';
			}

			echo $this->_helper->json(array(
				'success' => false,
				'status' => $status,
				'message' => $errorMsg,
				'type' => $type
			), false);
		}
	}
}