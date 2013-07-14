<?php 
/**
 * @package Var
 * @subpackage Auth
 */
class Form_AdminLogin extends Infra_Form
{
	public function init()
	{
		$this->setMethod('post');

		$this->addElement('hidden', 'timezone_offset', array(
			'decorators' => array('ViewHelper')
		));
		
		$this->setDecorators(array(
			'Description',
			'FormElements',
			array('Form', array('class' => 'login')),
		));
	}
}