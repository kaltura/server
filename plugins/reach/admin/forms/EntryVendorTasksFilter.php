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
			'idEqual' => 'Task ID',
			'vendorPartnerIdEqual' => 'Vendor Partner ID',
			'partnerIdEqual' => 'Partner ID'
		));

		$this->addElement('select', 'filter_status', array(
			'label' => 'Status',
			'filters' => array('StringTrim'),
			'decorators' => array('ViewHelper'),
			'multiOptions' => array(
				'' => 'Status',
				EntryVendorTaskStatus::READY => 'READY',
				EntryVendorTaskStatus::PENDING => 'PENDING',
				EntryVendorTaskStatus::SCHEDULED => 'SCHEDULED',
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

		$this->addElement('hidden', 'newLine1', array(
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'br', 'class' => 'newLine')))
		));

		$this->addElement('hidden', 'newLine2', array(
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'br', 'class' => 'newLine')))
		));

		$this->addElement('text', 'createdAtFrom', array(
			'label' => 'From Date:',
		));

		$this->addElement('hidden', 'newLine3', array(
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'br', 'class' => 'newLine')))
		));

		$this->addElement('text', 'createdAtTo', array(
			'label' => 'To Date:',
		));

		$this->addElement('hidden', 'crossLine', array(
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));

		$this->addElement('text', 'task_ids', array(
			'label' => 'Insert Pending Task ids (Separated by commas):',
		));

		$this->addElement('button', 'abort', array(
			'ignore' => true,
			'label' => 'ABORT TASKS',
			'onclick' => "abortTask($('#task_ids').val())",
			'decorators' => array('ViewHelper'),
		));
	}
}