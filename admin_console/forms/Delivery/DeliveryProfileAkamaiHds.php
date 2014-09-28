<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_DeliveryProfileAkamaiHds extends Form_Delivery_DeliveryProfileConfiguration
{
	
	public function getAdvancedSettings()
	{
		$element = $this->addElement('checkbox', 'supportClipping', array(
				'label'			=> 'Support clipping parameters:',
				'filters'		=> array('StringTrim'),
		));
		
		return array('supportClipping');
	}
	
}