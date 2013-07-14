<?php 
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_PartnerBaseFilter extends Infra_Form
{
	public function init()
	{
		$this->setMethod('post');
		
		$this->setDecorators(array(
			'FormElements', 
			'Form',
			array('HtmlTag', array('tag' => 'fieldset'))
		));
		
		// filter type
		$this->addElement('select', 'filter_type', array(
			'required' 		=> true,
			'multiOptions' 	=> array(
				'none' => 'None', 
				'byid' => 'Publisher ID',
				'byname' => 'Publisher Name'
			),
			'decorators' => array('ViewHelper', 'Label'),
		));
		
		// search input
		$this->addElement('text', 'filter_input', array(
			'required' 		=> true,
			'filters'		=> array('StringTrim'),
			'decorators' 	=> array('ViewHelper', array('HtmlTag', array('tag' => 'div', 'id' => 'filter_text')))
		));
		
		$this->addElement('text', 'filter_input_help', array(
			'decorators' => array(
				array('HtmlTag', array('tag' => 'div', 'class' => 'help', 'placement' => 'append')),
			)
		));
		
		
		$this->addDisplayGroup(array('filter_type', 'filter_input', 'filter_input_help' ), 'filter_type_group', array(
			'description' => 'partner-usage filter by',
			'decorators' => array(
				array('Description', array('tag' => 'legend', 'class' => 'partner_filter')), 
				'FormElements', 
				'Fieldset'
			)
		));
		
		
		// submit button
		$this->addElement('button', 'cmdSubmit', array(
			'type' => 'submit',
			'id' => 'do_filter',
			'label'		=> 'partner-usage filter search',
			'order'   => 100,
			'decorators' => array('ViewHelper'),
		));
	}
}