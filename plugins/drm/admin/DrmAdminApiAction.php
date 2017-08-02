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
		$actionApi = $this->_getParam('apiAction');
		
		$adminApiForm = new Form_AdminApiConfigure($partnerId, $drmType, $actionApi);
		KalturaLog::info("qwer - 1");
		try
		{
			if ($request->isPost())
			{
				KalturaLog::info("qwer - 2");
//				if ($actionApi == AdminApiActionType::REMOVE)
//					$res = $this->sendData($drmType, $partnerId, $actionApi);

				//$params = $request->getPost();

//				foreach($params as &$key =>$val) {
//					if (!$val)
//						unset($params[$key]);
//					else
//						$key = $this->translateName($key);
//				}
				$params = $this->getParams($request);


				KalturaLog::info("asdf");
				KalturaLog::info(print_r($params, true));
				
//				$params = array(); // get data params
				if ($actionApi == AdminApiActionType::ADD)
					$res = $this->sendData($drmType, $partnerId, $actionApi, $params);
				
				$action->view->formValid = true;
			}
			else
			{
				$res = $this->sendData($drmType, $partnerId, AdminApiActionType::GET);
				$adminApiForm->populate($res);
			}
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage() . PHP_EOL . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
		}	
		$action->view->form = $adminApiForm;
	}


	private function translateName($name)
	{
		$nameArray = array('provider_sign_key' => 'providerSignKey');
		if (isset($nameArray[$name]))
			return $this->nameArray[$name];
		return $name;
	}

	private function getParams($request)
	{
		$params = $request->getPost();
		$newParams = array();
		foreach($params as $key =>$val) {
			if ($val)
				$newParams[$this->translateName($key)] = $val;
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

	private function sendData($drmType, $partnerId, $action, $params = array())
	{
		list($host, $secret) = $this->getConfigParams();
		$body = $this->getBody($drmType, $partnerId, $params);
		$signature = DrmLicenseUtils::signDataWithKey($body,$secret);
		$url = $host . '/admin/' . $action . 'Partner' . '?signature=' . $signature;

		KalturaLog::debug("Send to UDRM server [$url] and body: [$body]");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,            $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POST,           1 );
		curl_setopt($ch, CURLOPT_POSTFIELDS,     $body);
		$result=curl_exec ($ch);
		curl_close($ch);
		KalturaLog::debug("Got response from UDRM server as [$result]");

		return $result;

	}

}

