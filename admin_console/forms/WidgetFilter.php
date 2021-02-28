<?php 
/**
 * @package Admin
 * @subpackage Widgets
 */
class Form_WidgetFilter extends Form_PartnerIdFilter
{
	public function init()
	{
		parent::init();
		$this->setAttrib('id', 'frmWidgetFilter');
		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
			'by-uiconf-id' => 'UI Conf ID',
			'by-uiconf-type' => 'UI Conf Type',
			'by-uiconf-name' => 'UI Conf Name',
			'by-uiconf-tags' => 'UI Conf Tags',
			'byid' => 'Publisher ID',
			'by-partner-name' => 'Publisher Name',
		));

		// filter object type
		$this->addElement('select', 'filter_obj_type_input', array(
			'required' 		=> true,
			'multiOptions' 	=> WidgetController::getSupportedUiConfTypes(),
			'hidden' => true,
			'decorators' => array('ViewHelper', 'Label'),
		));

		$this->addDisplayGroup(array('filter_type', 'filter_input', 'filter_obj_type_input'), 'filter_type_group', array(
			'decorators' => array(
				'FormElements',
			)
		));
	}
}