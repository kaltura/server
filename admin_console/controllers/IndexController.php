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
		$sessionExpiry = $settings->sessionExpiry;
		$kavaDashboardUrl = $settings->kavaDashboard->url;
		$jwtKey = $settings->kavaDashboard->jwtKey;
		$partnerId = $settings->partnerId;
		
		if(!$kavaDashboardUrl || !$jwtKey)
			$this->view->kavaDashboardUrl = null;
		else
			$this->view->kavaDashboardUrl = $this->generateSignedKavaDashboardUrl($kavaDashboardUrl, $jwtKey, $partnerId, $sessionExpiry);
	}
	
	private function generateSignedKavaDashboardUrl($kavaDashboardUrl, $jwtKey, $partnerId, $sessionExpiry)
	{
		$jwtPayload = array(
			'partnerId' => $partnerId,
			'iat' => time(),
			'exp' => time() + $sessionExpiry,
		);
		$jwt = $this->encode($jwtPayload, $jwtKey);
		return rtrim($kavaDashboardUrl, "/") . "/?jwt=" . $jwt;
	}
	
	private function urlsafeB64Encode($input)
	{
		return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
	}

	private function encode($payload, $key)
	{
		$header = array('typ' => 'JWT', 'alg' => 'HS256');
		$result = $this->urlsafeB64Encode(json_encode($header)) . '.' .
			$this->urlsafeB64Encode(json_encode($payload));
		$signature = hash_hmac('sha256', $result, $key, true);
		$result .= '.' . $this->urlsafeB64Encode($signature);
		return $result;
	}

}