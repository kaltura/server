<?php 
/**
 * @package Admin
 * @subpackage Users
 */
class Form_Partner_KmcUsersResetPassword extends Infra_Form
{
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('id', 'frmKmcUsersResetPassword');
		
		$this->addElement('password', 'newPassword', array(
			'label' 		=> 'New Password:',
			'required'		=> true,
			'filters' 		=> array('StringTrim'),	
		));
	}
}

