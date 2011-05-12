<?php 
class Form_Login extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');

		$this->addElement('text', 'email', array(
			'label'	  => 'Email address:',
			'required'   => true,
			'filters'	=> array('StringTrim'),
			'validators' => array(),
			'decorators' => array(
				'ViewHelper',
				'Label',
				array('HtmlTag', array('tag' => 'div', 'class' => 'item')) 
			)
		));
		
		$this->addElement('password', 'password', array(
			'label'	  => 'Password:',
			'required'   => true,
			'filters'	=> array('StringTrim'),
			'validators' => array(),
			'decorators' => array(
				'ViewHelper',
				'Label',
				array('HtmlTag', array('tag' => 'div', 'class' => 'item')) 
			)
		));
		
		$this->addElement('checkbox', 'remember_me', array(
			'label'	  => 'Remember Me?',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'div', 'class' => 'rememeber')))
		));
		
		$this->addElement('button', 'submit', array(
			'type' => 'submit',
			'ignore'   => true,
			'label'	=> 'Login',
			'decorators' => array('ViewHelper'),
		));
		
		$this->addElement('hidden', 'next_uri', array(
			'decorators' => array('ViewHelper')
		));
		
		$this->setDecorators(array(
			'Description',
			'FormElements',
			array('Form', array('class' => 'login')),
		));
	}
}