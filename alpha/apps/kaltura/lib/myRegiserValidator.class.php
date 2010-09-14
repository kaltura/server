<?php 

class myRegisterValidator extends sfValidator 
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
		//$password_param = $this->getParameter('password'); 
		//$password = $this->getContext()->getRequest()->getParameter($password_param); 

		$login = $value; 
		
		$c = new Criteria(); 
		$c->add(kuserPeer::SCREEN_NAME, $login); 
		$user = kuserPeer::doSelectOne($c);
		// screen name already exists? 
		if($user) 
		{ 
			$error = 'Screename already exists'; 
			return false; 
			
		} 
		else 
		{ 
			//add user to DB
			
			
			return true;
		}
	} 
}

?>