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

		$this->removeElement("cmdSubmit");

		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
			'vendorPartnerIdEqual' => 'Vendor Partner ID',
			'partnerIdEqual' => 'Partner ID'
		));

		$this->addElement('select', 'filter_status', array(
			'label' => 'Status',
			'filters' => array('StringTrim'),
			'multiOptions' => array(
				'' => '',
				EntryVendorTaskStatus::PENDING => 'PENDING',
				EntryVendorTaskStatus::PROCESSING => 'PROCESSING',
				EntryVendorTaskStatus::ERROR => 'ERROR'
		)));

		$this->addElement('text', 'from_time', array(
			'label' => 'Due Date: Insert -/+ and minutes',
			'filters' => array('StringTrim'),
			'validators' => array('Int'),
		));
		$this->setDefault('from_time', "Enter Relative Time");

		// submit button
		$this->addElement('button', 'cmdSubmit', array(
			'type' => 'submit',
			'label'		=> 'Search',
			'decorators' => array('ViewHelper'),
		));
	}
}