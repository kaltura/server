<?php 
/**
 * @package Admin
 * @subpackage Reports
 */
class Form_Report extends Infra_Form
{
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('class', 'report-form');
		

		$this->addElement('text', 'id', array(
			'label'			=> 'Report ID:',
			'required'		=> false,
			'disabled' 		=> true,
			'filters'		=> array('StringTrim'),
			'validators' 	=> array()
		));
		
		$this->addElement('text', 'name', array(
			'label'			=> 'Name:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'validators' 	=> array()
		));
		
		$this->addElement('text', 'description', array(
			'label'			=> 'Description:',
			'filters'		=> array('StringTrim'),
			'validators' 	=> array()
		));
		
		$this->addElement('text', 'system_name', array(
			'label'			=> 'Sytem Name:',
			'readonly'		=> true,
		));
		
		$this->addElement('text', 'partner_id', array(
			'label'			=> 'Publisher ID:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('textarea', 'query', array(
			'label'			=> 'Query:',
			'required'		=> true,
			'validators' 	=> array(),
			'decorators'	=> array('ViewHelper')
		));
	}
}