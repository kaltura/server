<?php 
class Form_WidgetFilter extends Form_PartnerFilter
{
	public function init()
	{
		parent::init();
		$this->setAttrib('id', 'frmWidgetFilter');
		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
			'none' => 'None',
			'by-uiconf-id' => 'Widget ID', 
			'by-partner-id' => 'Publisher ID',
		));
	}
}