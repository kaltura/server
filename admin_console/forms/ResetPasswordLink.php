<?php 
class Form_ResetPasswordLink extends Zend_Form
{
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		
		$this->addElement('password', 'newPassword', array(
			'label'	  => 'New password:',
			'required'   => true,
			'filters'	=> array('StringTrim'),
			'validators' => array(),
		));
		
		$this->addElement('password', 'newPasswordConfirm', array(
			'label'	  => 'Confirm new password:',
			'required'   => true,
			'filters'	=> array('StringTrim'),
			'validators' => array(),
		));
		
		$this->addElement('button', 'submit', array(
			'type' => 'submit',
			'ignore'   => true,
			'label'	=> 'Set password',
			'decorators' => array('ViewHelper'),
		));
				
		
		$this->setDecorators(array(
			'Description',
			'FormElements',
			array('Form', array('class' => 'login')),
		));
		
		

	}
	
	public function isValid($data)
	{
	    // validate that the value given for the 'newPasswordConfirm' field is identical to the 'newPassword' field
	    $validator = new Zend_Validate_Identical($data['newPassword']);
	    $validator->setMessages(array(
	        Zend_Validate_Identical::NOT_SAME      => 'Passwords do not match',
	        Zend_Validate_Identical::MISSING_TOKEN => 'Passwords do not match'
	    ));
	    $this->getElement('newPasswordConfirm')->addValidator($validator);
	
	    return parent::isValid($data);
	}
		
}