<?php 
/**
 * @package Admin
 * @subpackage Auth
 */
class Form_AssignPartners extends Infra_Form
{
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('class', 'form');
		$this->setAttrib('id','AssignPartnersForm');
		$this->setDescription('user assign partners');
		$this->loadDefaultDecorators();
		$this->addDecorator('Description', array('placement' => 'prepend'));
		
		
		// Add a name element
		$this->addElement('text', 'name', array(
			'label'			=> 'User Name:',
			'filters'		=> array('StringTrim'),
			'readonly'		=> true,
			'ignore' 		=> true,
		));
		
		$this->addElement('textarea', 'partners', array(
			'label' => 'Partner Ids:',
			'filters'		=> array('StringTrim'),
			'cols' => '19',
			'rows' => '8',
		));
		
		
		//partner package
		$this->addElement('multiCheckbox', 'partner_package', array(
			'filters'		=> array('StringTrim'),
			'decorators' => array('ViewHelper', 'Label'),
		));
		$this->getElement('partner_package')->setLabel('Allowed Partner Service Editions:<br />');
		$this->getElement('partner_package')->getDecorator('Label')->setOption('escape', false);
		
		
		
	}
}