<?php 

class myPasswordMatchValidator extends sfValidator 
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

		$repeatpassword_param = $this->getParameter('repeatpassword'); 
		$repeatpassword = $this->getContext()->getRequest()->getParameter($repeatpassword_param); 
		
		// screen name already exists? 
		if( $password != $repeatpassword) 
		{ 
			$error = 'Passwords do not match'; 
			return false; 
			
		} 
		else return true;
	} 
}

?>