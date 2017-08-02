<?php
/**
 * @package plugins.drm
 * @subpackage Admin
 */
class DrmAdminApiAction extends KalturaApplicationPlugin
{
	const HOST = 'http://winderd:81';
	const SECRET = 'asdf';
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

		try
		{
			if ($request->isPost())
			{
				KalturaLog::info("qwer - got post");
				
				
				if ($actionApi == AdminApiActionType::REMOVE)
					$res = $this->sendData($drmType, $partnerId, $actionApi);

				$params = array(); // get data params
				if ($actionApi == AdminApiActionType::ADD)
					$res = $this->sendData($drmType, $partnerId, $actionApi, $params);


			}
			else
			{
				$res = $this->sendData($drmType, $partnerId, AdminApiActionType::GET);
				if ($res)
					$adminApiForm->populate(json_decode($res, true));
			}
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage() . PHP_EOL . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
		}	
		$action->view->form = $adminApiForm;
	}

	private function getBody($drmType, $partnerId, $params)
	{
		$body = "drmType=$drmType&partnerId=$partnerId";
		foreach($params as $key => $value)
			$body .= "&$key=$value";
		return $body;
	}

	private function sendData($drmType, $partnerId, $action, $params = array())
	{
		//$host = kConf::get('license_server_url', 'drm', null);
		//$secret = kConf::get('admin_secret', 'drm', null);
		//KalturaLog::info("qwer - $host $secret");

		if (!self::SECRET || !self::HOST)
		{
			KalturaLog::info("Missing configuration Params. check for UDRM server host or Admin secret");
			return null;
		}

		$body = $this->getBody($drmType, $partnerId, $params);
		$signature = DrmLicenseUtils::signDataWithKey($body,self::SECRET);
		$url = self::HOST . '/admin/' . $action . 'Partner' . '?signature=' . $signature;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,            $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POST,           1 );
		curl_setopt($ch, CURLOPT_POSTFIELDS,     $body);
		$result=curl_exec ($ch);
		KalturaLog::debug("Got response from UDRM server as [$result]");
		return $result;

	}

}

