<?php 
/**
 * @package plugins.hostedReports
 */
class DateRangeFilter extends Infra_Form
{
	public function init()
	{
		$this->setMethod('post');
		
		$this->setDecorators(array(
			'FormElements', 
			'Form',
			array('HtmlTag', array('tag' => 'fieldset'))
		));
		
		//date range
		$this->addElement('select', 'date_range', array(
			'required' 		=> true,
			'multiOptions' 	=> array(
				'yesterday' => 'Yesterday',
		        'last_7_days' => 'Last 7 Days',
		        'week'		=> 'This week',
		        'last_week' => 'Last week',
		        'last_30_days' => 'Last 30 days',
		        'this_month'   => 'This month',
		        'last_month'   => 'Last month',
		        'last_12_months' => 'Last 12 months',
		        'this_year' =>    'This year',
		        'custom' => 'Custom',
			),
			'value'		=> 'last_30_days',
			'decorators' => array('ViewHelper', 'Label'),
		));
		
		// from
		$from = new Zend_Date(time() - (60*60*24*31));
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
		$to = new Zend_Date(time() - 2*24*60*60);
		$this->addElement('text', 'to_date', array(
			'value' 		=> $to->toString(self::getDefaultTranslator()->translate('datepicker format')), 
			'filters'		=> array('StringTrim'),
			'decorators' 	=> array('ViewHelper')
		));
		
		$this->addDisplayGroup(array('date_range'), 'date_range_group', array(
			'description' => 'partner-usage date-range filter type',
			'decorators' => array(
				array('Description', array('tag' => 'legend')), 
				'FormElements', 
				'Fieldset'
			)
		));
		
		$this->addDisplayGroup(array('from_date', 'dates_separator', 'to_date'), 'dates', array(
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