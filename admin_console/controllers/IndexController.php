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
		if (!Infra_AclHelper::isAllowed('developer', 'entry-restoration'))
		{
			return;
		}

		$this->view->errors = array();
		$this->view->results = null;
		$this->view->isDryRun = false;

		if ($this->getRequest()->isPost())
		{
			try {
				$partnerId = $this->getRequest()->getParam('partnerId');
				$inputMode = $this->getRequest()->getParam('inputMode', 'textarea');
				$dryRun = $this->getRequest()->getParam('dryRun', 'false') === 'true';
				$entryIdsString = '';

				// Validate partner ID
				if (empty($partnerId) || !is_numeric($partnerId))
				{
					$this->view->errors[] = "Invalid Partner ID";
					return;
				}

				// Get entry IDs based on input mode
				if ($inputMode === 'file')
				{
					// Handle file upload
					if (!empty($_FILES['entryFile']['tmp_name']))
					{
						// Validate file size (max 5MB)
						if ($_FILES['entryFile']['size'] > 5 * 1024 * 1024)
						{
							$this->view->errors[] = "File is too large. Maximum size is 5MB";
							return;
						}

						// Validate file extension
						$fileName = $_FILES['entryFile']['name'];
						$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
						if (!in_array($fileExtension, array('txt', 'csv')))
						{
							$this->view->errors[] = "Invalid file type. Only .txt and .csv files are allowed";
							return;
						}

						$fileContent = file_get_contents($_FILES['entryFile']['tmp_name']);
						if ($fileContent === false)
						{
							$this->view->errors[] = "Failed to read uploaded file";
							return;
						}

						// Validate that file contains at least one entry ID
						$fileContent = trim($fileContent);
						if (empty($fileContent))
						{
							$this->view->errors[] = "Uploaded file is empty";
							return;
						}

						// Basic validation - check if content looks like entry IDs
						// Entry IDs follow format: {dc_id}_{random_string} where dc_id is datacenter ID
						// and random_string is typically 8-10 characters alphanumeric (e.g., 0_abc123xy, 0_abc123xy12)
						$lines = preg_split('/[\r\n,]+/', $fileContent);
						$validEntryIdPattern = '/^[0-9]+_[a-z0-9]{8,}$/i';
						$hasValidEntryId = false;

						foreach ($lines as $line)
						{
							$line = trim($line);
							if (!empty($line))
							{
								if (preg_match($validEntryIdPattern, $line))
								{
									$hasValidEntryId = true;
									break;
								}
							}
						}

						if (!$hasValidEntryId)
						{
							$this->view->errors[] = "File does not contain valid entry IDs. Entry IDs should be in format: 0_abc123xy (datacenter_alphanumeric)";
							return;
						}

						$entryIdsString = $fileContent;
					}
					else
					{
						$this->view->errors[] = "No file uploaded";
						return;
					}
				}
				else
				{
					// Handle textarea or single input
					$entryIdsString = $this->getRequest()->getParam('entryIds', '');
					if (empty($entryIdsString))
					{
						$this->view->errors[] = "No entry IDs provided";
						return;
					}
				}

				// Initialize Kaltura client
				$client = Infra_ClientHelper::getClient();
				// Do NOT set partner ID - keep it as admin console partner (-2)
				// so the service permission check passes
				// $client->setPartnerId($partnerId);

				// Prepare bulk restore data
				$bulkRestoreData = new Kaltura_Client_AdminConsole_Type_BulkRestoreEntryData();
				$bulkRestoreData->partnerId = $partnerId;
				$bulkRestoreData->entryIds = $entryIdsString;
				$bulkRestoreData->dryRun = $dryRun;

				// Call the bulk restore API
				$entryAdminPlugin = Kaltura_Client_AdminConsole_Plugin::get($client);
				$response = $entryAdminPlugin->entryAdmin->bulkRestoreDeletedEntries($bulkRestoreData);

				$this->view->results = $response->objects;
				$this->view->isDryRun = $dryRun;

			} catch (Exception $e) {
				$this->view->errors[] = "Error: " . $e->getMessage();
			}
		}
	}
}