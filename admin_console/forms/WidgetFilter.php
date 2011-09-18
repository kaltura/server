<?php 
class Form_WidgetFilter extends Form_PartnerBaseFilter
{
	public function init()
	{
		parent::init();
		$this->setAttrib('id', 'frmWidgetFilter');
		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
			'none' => 'None',
			'by-uiconf-id' => 'UI Conf ID',  
			'by-partner-id' => 'Publisher ID',
			'by-partner-name' => 'Publisher Name',
		));
	}
}