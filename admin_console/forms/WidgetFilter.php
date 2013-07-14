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
			'byid' => 'Publisher ID',
			'by-partner-name' => 'Publisher Name',
		));
	}
}