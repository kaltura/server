<?php 
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_PartnerFilter extends Form_PartnerBaseFilter
{
	public function init()
	{
		parent::init();
		
		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
				'none' => 'None', 
				'byid' => 'Publisher ID',
				'byname' => 'Publisher Name',
				'free' => 'Free-form text',
				'byEntryId' => 'Entry ID',
		                'byUIConfId' => 'UI Conf ID'
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
		
		
		$this->addElement('select', 'partner_package', array(		
			'filters'		=> array('StringTrim'),
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')))
		));
		
		$this->addDisplayGroup(array('include_active', 'include_blocked', 'include_removed'), 'statuses', array(
			'description' => 'partner-usage filter status types',
			'decorators' => array(
				array('Description', array('tag' => 'legend', 'class' => 'partner_filter')), 
				'FormElements', 
				'Fieldset'
			)
		));
		
		$this->addDisplayGroup(array('partner_package'), 'partnerPackage', array(
			'description' => 'Show Service Editions:', 
			'decorators' => array(
				array('Description', array('tag' => 'legend', 'class' => 'partner_filter')), 
				'FormElements', 
				'Fieldset',
			)
		));
		
	}
}