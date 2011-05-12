<?php 
class Form_VirusScanFilter extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'frmVirusScanFilter');
		
		$this->setDecorators(array(
			'FormElements', 
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));
		
		// filter type
		$this->addElement('select', 'filter_type', array(
			'required' 		=> true,
			'label' => 'partner-filter filter by',
			'multiOptions' 	=> array(
				'none' => 'None', 
				'partnerIdIn' => 'Publisher ID',
				'idIn' => 'Profile ID',
				'nameLike' => 'Profile Name',
			),
			'decorators' => array('ViewHelper', 'Label'),
		));
		
		// search input
		$this->addElement('text', 'filter_input', array(
			'required' 		=> true,
			'filters'		=> array('StringTrim'),
			'decorators' 	=> array(
				'ViewHelper', 
				array('HtmlTag', array('tag' => 'div', 'id' => 'filter_text')),
			)
		));
		

		$this->addDisplayGroup(array('filter_type', 'filter_input', 'filter_input_help'), 'filter_type_group', array(
			'decorators' => array(
				'FormElements', 
			)
		));
		
		// submit button
		$this->addElement('button', 'cmdSubmit', array(
			'type' => 'submit',
			'label'		=> 'Search',
			'decorators' => array('ViewHelper'),
		));
	}
}