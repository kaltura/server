<?php
/**
 * @package plugins.drm
 * @subpackage Admin
 */
class DrmAdminApiAction extends KalturaApplicationPlugin
{
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('layout')->setLayout('layout_empty');
		$request = $action->getRequest();

		$partnerId = $this->_getParam('pId');
		$drmType = $this->_getParam('drmType');
		$actionApi = $this->_getParam('adminApiAction');

		if (!DrmPlugin::isAllowAdminApi($actionApi))
		{
			$action->view->errMessage = 'You do not have permission for this action';
			return;
		}
		
		$adminApiForm = new Form_AdminApiConfigure($partnerId, $drmType, $actionApi);
		KalturaLog::info("Got params for the ADMIN-API action as: partnerId: [$partnerId], drmType: [$drmType] ,actionApi: [$actionApi] ");
		
		try
		{
			if ($request->isPost())
			{
				if ($actionApi !== AdminApiActionType::GET)
				{
					$this->sendData($drmType, $partnerId, $actionApi, $actionApi, $this->getParams($request));
				}
				$action->view->formValid = true;
			}
			else
			{
				$res = $this->sendData($drmType, $partnerId, AdminApiActionType::GET, $actionApi);
				$adminApiForm->populateJson($res);
			}
			
			$action->view->form = $adminApiForm;
		}
		catch(Exception $e)
		{
			$action->view->errMessage = $e->getMessage();
		}
	}
	
	private function getParams($request)
	{
		$params = $request->getPost();
		$newParams = array();
		foreach($params as $key =>$val)
		{
			if ($val)
			{
				$newParams[$key] = $val;
			}
		}
		return $newParams;
	}

	private function getBody($drmType, $partnerId, $params)
	{
		$body = "drmType=$drmType&partnerId=$partnerId";
		foreach($params as $key => $value)
			$body .= "&$key=$value";
		return $body;
	}

	private function getConfigParams()
	{
		$host = Zend_Registry::get('config')->admin_api_server_url;
		$secret = Zend_Registry::get('config')->admin_request_secret;
		KalturaLog::info("Got configuration params as: host [$host] secret [$secret] ");
		if (!$secret || !$host)
			throw new Exception("Missing configuration Params. check for UDRM server host or Admin secret");
		return array($host, $secret);
	}

	private function sendData($drmType, $partnerId, $actionToPerform, $originalAction, $params = array())
	{
		list($host, $secret) = $this->getConfigParams();
		$body = $this->getBody($drmType, $partnerId, $params);
		$signature = DrmLicenseUtils::signDataWithKey($body,$secret);
		$url = $host . '/admin/' . $actionToPerform . 'Partner' . '?signature=' . $signature;

		KalturaLog::debug("Send to UDRM server [$url] and body: [$body]");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,            $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POST,           1 );
		curl_setopt($ch, CURLOPT_TIMEOUT,        10 );
		curl_setopt($ch, CURLOPT_POSTFIELDS,     $body);
		$result=curl_exec ($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		KalturaLog::debug("Got response from UDRM server as [$result]");

		if($httpCode === KCurlHeaderResponse::HTTP_STATUS_NOT_FOUND)
		{
			if ($originalAction === AdminApiActionType::ADD)
			{
				return '{}';
			}

			throw new Exception("The record [{$drmType}_$partnerId] does not exist");
		}
		
		if($httpCode !== KCurlHeaderResponse::HTTP_STATUS_OK)
		{
			throw new Exception("Got bad HTTP response with code ($httpCode)");
		}
		
		if(!$result)
		{
			throw new Exception("Got empty response");
		}
		
		return $result;

	}
}
