<?php 
/**
 * @package plugins.confMaps
 * @subpackage Admin
 */
class Form_ConfigurationMapFilter extends Form_PartnerIdFilter
{
	public function init()
	{
		parent::init();

		$this->removeElement("cmdSubmit");
		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
			'ConfigurationMapNameEqual' => 'Map Name'
		));

		// filter by vendor partner id
		$this->addElement('text', 'filterHostName', array(
			'label' => 'Host Name',
			'decorators' => array('ViewHelper', 'Label')
		));


		// submit button
		$this->addElement('button', 'cmdSubmit', array(
			'type' => 'submit',
			'label'		=> 'Search',
			'decorators' => array('ViewHelper'),
		));

		// submit button
		$this->addElement('button', 'newConfigurationMap', array(
			'ignore' => true,
			'label' => 'Create New',
			'onclick' => "addNewConfigurationMap($('#filter_input').val(), $('#filterHostName').val(),)",
			'decorators' => array('ViewHelper'),
		));
	}
}
