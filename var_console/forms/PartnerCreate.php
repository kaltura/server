<?php 
/**
 * @package Var
 * @subpackage Partners
 */
class Form_PartnerCreate extends Infra_Form
{	    
	public function init()
	{
		parent::init();
		
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setName('new_account'); // form id
		$this->setAttrib('class', 'inline-form');
		
		$this->addElement('text', 'admin_name', array(
			'label' => 'partner-create form name',
			'required' => true,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'name', array(
			'label' => 'partner-create form company',
			'required' => true,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'admin_email', array(
			'label' => 'partner-create form admin email',
			'required' => true,
			'validators' => array('EmailAddress'),
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'phone', array(
			'label' => 'partner-create form admin phone',
			'required' => true,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'website', array(
			'label' => 'partner-create form url',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('select', 'copyPartner', array(
		    'label' => 'partner-create from copy partner',
		    'filters'		=> array('StringTrim'),
			'required' 		=> true,
		    'RegisterInArrayValidator' => false
		));

		$this->addElement('text', 'reference_id', array(
				'label' => 'partner-create referenceId',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addDisplayGroup(array('admin_name', 'name', 'admin_email', 'phone', 'describe_yourself', 'website', 'copyPartner', 'reference_id'), 'partner_info', array(
			'legend' => 'Account Info',
			'decorators' => array(
				'Description', 
				'FormElements', 
				array('Fieldset'),
			)
		));
		
		$this->addElement('button', 'submit', array(
			'label' => 'partner-create form create',
			'type' => 'submit',
			'decorators' => array('ViewHelper'),
		));
		
		
		$this->addDisplayGroup(array('submit'), 'buttons1', array(
			'decorators' => array(
				'FormElements', 
				array('HtmlTag', array('tag' => 'div', 'class' => 'buttons')),
			)
		));
	}
	
    public function setProviders(array $providers)
	{
		$element = $this->getElement('copyPartner');
		foreach($providers as $type => $name)
			$element->addMultiOption($type, $name);
	}
}