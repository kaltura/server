<?php 
class Form_PartnerUsageFilter extends Zend_Form
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
				'byname' => 'Publisher Name',
				'free' => 'Free-form text'
			),
			'decorators' => array('ViewHelper', 'Label'),
		));
		
		// search input
		$this->addElement('text', 'filter_input', array(
			'required' 		=> true,
			'filters'		=> array('StringTrim'),
			'decorators' 	=> array('ViewHelper', array('HtmlTag', array('tag' => 'div', 'id' => 'filter_text')))
		));
		
		// active status
		$this->addElement('checkbox', 'include_active', array(
			'label' => 'partner-usage filter active',
			'checked' => true,
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')))
		));
		
		// blocked status
		$this->addElement('checkbox', 'include_blocked', array(
			'label' => 'partner-usage filter blocked',
			'checked' => true,
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')))
		));
		
		// removed status
		$this->addElement('checkbox', 'include_removed', array(
			'label' => 'partner-usage filter removed',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')))
		));
		
		// from
		$from = new Zend_Date(time() - (60*60*24*30));
		$this->addElement('text', 'from_date', array(
			'value' 		=> $from->toString(self::getDefaultTranslator()->translate('datepicker format')),
			'filters'		=> array('StringTrim'),
			'decorators' 	=> array('ViewHelper')
		));
		
		// from - to separator
		$this->addElement('text', 'dates_separator', array(
			'description' 		=> '&nbsp;-&nbsp;',
			'filters'		=> array('StringTrim'),
			'decorators' 	=> array(
				array('Description', array('escape' => false, 'tag' => ''))
			)
		));

		// to
		$to = new Zend_Date(time());
		$this->addElement('text', 'to_date', array(
			'value' 		=> $to->toString(self::getDefaultTranslator()->translate('datepicker format')), 
			'filters'		=> array('StringTrim'),
			'decorators' 	=> array('ViewHelper')
		));
		
		$this->addElement('text', 'clear_dates', array(
			'description' => 'partner-usage filter clear dates',
			'decorators' 	=> array(array('Description', array('tag' => 'a', 'id' => 'clear_dates')))
		));
		
		$this->addElement('text', 'filter_input_help', array(
			'decorators' => array(
				array('HtmlTag', array('tag' => 'div', 'class' => 'help', 'placement' => 'append')),
			)
		));
		
		$this->addDisplayGroup(array('filter_type', 'filter_input', 'filter_input_help'), 'filter_type_group', array(
			'description' => 'partner-usage filter by',
			'decorators' => array(
				array('Description', array('tag' => 'legend')), 
				'FormElements', 
				'Fieldset'
			)
		));
		
		
		$this->addDisplayGroup(array('include_active', 'include_blocked', 'include_removed'), 'statuses', array(
			'description' => 'partner-usage filter status types',
			'decorators' => array(
				array('Description', array('tag' => 'legend')), 
				'FormElements', 
				'Fieldset'
			)
		));
		
		$this->addDisplayGroup(array('from_date', 'dates_separator', 'to_date', 'clear_dates'), 'dates', array(
			'description' => 'partner-usage filter range limit', 
			'decorators' => array(
				array('Description', array('tag' => 'legend')), 
				'FormElements', 
				'Fieldset',
			)
		));
		
		// submit button
		$this->addElement('button', 'submit', array(
			'type' => 'submit',
			'id' => 'do_filter',
			'label'		=> 'partner-usage filter search',
			'decorators' => array('ViewHelper'),
		));
	}
}