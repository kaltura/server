<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_DeliveryProfileAkamaiHttp extends Form_Delivery_DeliveryProfileConfiguration
{
	
	public function getAdvancedSettings()
	{
		$element = $this->addElement('checkbox', 'useIntelliseek', array(
				'label'			=> 'Use Intelliseek:',
				'filters'		=> array('StringTrim'),
		));
		
		return array('useIntelliseek');
	}
	
}