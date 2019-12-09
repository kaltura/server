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

		$this->removeElement("filter_input");
		$this->addElement('select', 'filter_input', array(
			'required'	=> true,
			'filters'	=> array('StringTrim'),
			'decorators'	=> array(
				'ViewHelper',
				array('HtmlTag', array('tag' => 'div', 'id' => 'filter_text')),
			)
		));

		// filter by vendor partner id
		$this->addElement('text', 'filterHostName', array(
			'label' => 'Host Name',
			'decorators' => array('ViewHelper', 'Label')
		));

		// search button
		$this->addElement('button', 'cmdSubmit', array(
			'type' => 'submit',
			'label'		=> 'Search',
			'decorators' => array('ViewHelper'),
		));

		// view button
		$this->addElement('button', 'viewConfigurationMapButton', array(
			'ignore' => true,
			'label' => 'View',
			'onclick' => "viewConfigurationMap($('#filter_input').val(), $('#filterHostName').val())",
			'decorators' => array('ViewHelper'),
		));

		// submit button
		$this->addElement('button', 'newConfigurationMap', array(
			'ignore' => true,
			'label' => 'Create New',
			'onclick' => "addNewConfigurationMap($('#filter_input').val(), $('#filterHostName').val())",
			'decorators' => array('ViewHelper'),
		));

		// filter by version
		$this->addElement('text', 'filterVersion', array(
			'label' => 'version',
			'decorators' => array('ViewHelper', 'Label')
		));

		// view specific version button
		$this->addElement('button', 'viewSpecificVersionButton', array(
			'ignore' => true,
			'label' => 'View Specific Version',
			'onclick' => "viewConfigurationMap($('#filter_input').val(), $('#filterHostName').val(),$('#filterVersion').val())",
			'decorators' => array('ViewHelper'),
		));
	}
}
