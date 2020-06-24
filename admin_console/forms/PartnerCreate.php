<?php 
/**
 * @package Admin
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

		$this->addElement('text', 'name', array(
			'label' => 'partner-create form name',
			'required' => true,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'company', array(
			'label' => 'partner-create form company',
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
		
		$this->addElement('select', 'partner_package', array(
			'label'			=> 'partner-create form package',
			'filters'		=> array('StringTrim'),
			'required' 		=> true,
		));
		
		$this->addElement('select', 'partner_package_class_of_service', array(		
			'label'			=> 'Class of Service:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('select', 'vertical_clasiffication', array(		
			'label'			=> 'Vertical Classification:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'website', array(
			'label' => 'partner-create form url',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 'additional_param_1_key', array(
			'label'			=> 'partner-create form key 1',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 'additional_param_1_val', array(
			'label'			=> 'partner-create form val 1',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 'additional_param_2_key', array(
			'label'			=> 'partner-create form key 2',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 'additional_param_2_val', array(
			'label'			=> 'partner-create form val 2',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('select', 'partner_template_id', array(		
			'label'			=> 'Select Template Partner ID:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('select', 'partner_language', array(		
			'label'			=> "Select partner's UI language:",
			'filters'		=> array('StringTrim'),
		));
		
		$this->addDisplayGroup(array('name', 'company', 'admin_email', 'phone', 'describe_yourself', 'partner_package', 'partner_package_class_of_service' , 'vertical_clasiffication', 'partner_language' , 'partner_template_id'), 'partner_info', array(
			'legend' => 'Publisher Info',
			'decorators' => array(
				'Description', 
				'FormElements', 
				array('Fieldset'),
			)
		));
		
		$this->addDisplayGroup(array('website', 'content_categories', 'adult_content'), 'website_info', array(
			'legend' => 'Website Info',
			'decorators' => array(
				'Description', 
				'FormElements', 
				array('Fieldset'),
			)
		));

		$this->addDisplayGroup(array('additional_param_1_key', 'additional_param_1_val'), 'additional_param_1', array(
			'legend' => 'Additional Param 1',
			'decorators' => array(
				'Description',
				'FormElements',
				array('Fieldset'),
			)
		));

		$this->addDisplayGroup(array('additional_param_2_key', 'additional_param_2_val'), 'additional_param_2', array(
			'legend' => 'Additional Param 2',
			'decorators' => array(
				'Description',
				'FormElements',
				array('Fieldset'),
			)
		));

		$this->addElement('button', 'submit', array(
			'label' => 'partner-create form create',
			'type' => 'submit',
			'decorators' => array('ViewHelper')
		));
		
		
		$this->addDisplayGroup(array('submit'), 'buttons1', array(
			'decorators' => array(
				'FormElements', 
				array('HtmlTag', array('tag' => 'div', 'class' => 'buttons')),
			)
		));
	}
}