<?php 
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_PartnerIdFilter extends Infra_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'frmPartnerIdFilter');
		
		$this->setDecorators(array(
			'FormElements', 
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));
		
		// filter type
		$this->addElement('select', 'filter_type', array(
			'required' 		=> true,
			'label' => 'Filter by',
			'multiOptions' 	=> array(
				'none' => 'None', 
				'byid' => 'Publisher ID',
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
		
		//$this->addElement('text', 'filter_input_help', array(
		//	'decorators' => array(
		//		array('HtmlTag', array('tag' => 'div', 'class' => 'help', 'placement' => 'append')),
		//	)
		//));
		
		$this->addDisplayGroup(array('filter_type', 'filter_input'), 'filter_type_group', array(
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