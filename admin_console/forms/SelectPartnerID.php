<?php 
class Form_SelectPartnerID extends Zend_Form
{
	public function init()
	{
		$this->setMethod('get');
		$this->setAttrib('class', 'inline-form');

		$this->addElement('text', 'partner_id', array(
			'label'	  => 'Partner ID:',
			'required'   => true,
			'filters'	=> array('StringTrim'),
			'validators' => array(),
			'decorators' => array(
				'ViewHelper',
				'Label',
				array('HtmlTag', array('tag' => 'div', 'class' => 'item')) 
			)
		));
		
		$this->addDisplayGroup(array('partner_id'), 'partner_id_group', array(
			'decorators' => array(
				'Description', 
				'FormElements', 
				array('Fieldset'),
			)
		));
		
		$this->addElement('button', 'submit', array(
			'label' => 'Continue',
			'type' => 'submit',
			'decorators' => array('ViewHelper')
		));
		
		
		$this->addDisplayGroup(array('submit'), 'submit_group', array(
			'decorators' => array(
				'FormElements', 
				array('HtmlTag', array('tag' => 'div', 'class' => 'buttons')),
			)
		));
	}
}