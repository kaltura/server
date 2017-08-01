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
				//if remove  -> exe
				// if add -> exe
			}
			else
			{
				$res = $this->getDoc($drmType, $partnerId);

				KalturaLog::debug("Got response from UDRM server as [$res]");
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

	private static function createUDRMSignature($secret, $msg)
	{
		$sha1 = sha1($secret.$msg, true);
		$b64 = base64_encode($sha1);
		return urlencode($b64);
	}

	private function sendPost($body, $path)
	{
		$signature = $this->createUDRMSignature(self::SECRET, $body);
		$url = self::HOST . $path . '?signature=' . $signature;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,            $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POST,           1 );
		curl_setopt($ch, CURLOPT_POSTFIELDS,     $body);
		$result=curl_exec ($ch);
		return $result;

	}
	
	private function getDoc($drmType, $partnerId)
	{
		$body = "drmType=$drmType&partnerId=$partnerId";
		$path = '/admin/getPartner';
		return $this->sendPost($body, $path);

	}

	private function removeDoc($drmType, $partnerId)
	{
		$body = "drmType=$drmType&partnerId=$partnerId";
		$path = '/admin/removePartner';
		return $this->sendPost($body, $path);
	}

	private function addDoc($drmType, $partnerId, $params)
	{
		$body = "drmType=$drmType&partnerId=$partnerId";
		foreach($params as $key => $value)
			$body .= "&$key=$value";
		$path = '/admin/addPartner';
		return $this->sendPost($body, $path);
	}
	
}

