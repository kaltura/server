<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_EntryVendorTasksFilter extends Form_PartnerIdFilter
{
	public function init()
	{
		parent::init();
		$this->setAttrib('id', 'frmReachRequestsList');

		$this->removeElement("cmdSubmit");

		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
			'vendorPartnerIdEqual' => 'Vendor Partner ID',
			'partnerIdEqual' => 'Partner ID'
		));

		$this->addElement('select', 'filter_status', array(
			'label' => 'Status',
			'filters' => array('StringTrim'),
			'decorators' => array('ViewHelper'),
			'multiOptions' => array(
				'' => 'Status',
				EntryVendorTaskStatus::PENDING => 'PENDING',
				EntryVendorTaskStatus::PROCESSING => 'PROCESSING',
				EntryVendorTaskStatus::ERROR => 'ERROR'
		)));

		$this->addElement('text', 'from_time', array(
			'label' => 'Insert -/+hours for relative time',
			'filters' => array('StringTrim'),
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')))
		));
		$this->setDefault('from_time', 'Due_Date');

		// submit button
		$this->addElement('button', 'cmdSubmit', array(
			'type' => 'submit',
			'label'		=> 'Search',
			'decorators' => array('ViewHelper'),
		));

		$this->addElement('button', 'exportCsv', array(
			'ignore' => true,
			'label' => 'Export to CSV',
			'onclick' => "exportToCsv($('#filter_type').val(), $('#filter_input').val(), $('#filter_status').val(), $('#from_time').val())",
			'decorators' => array('ViewHelper'),
		));
	}
}