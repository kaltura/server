<?php 
class Form_PartnerFilter extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'frmPartnerFilter');
		
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
				'byid' => 'Publisher ID',
				'byname' => 'Publisher Name',
				'free' => 'Free-form text'
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
		
		$this->addElement('text', 'filter_input_help', array(
			'decorators' => array(
				array('HtmlTag', array('tag' => 'div', 'class' => 'help', 'placement' => 'append')),
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
			'label'		=> 'partner-filter search',
			'decorators' => array('ViewHelper'),
		));
	}
}