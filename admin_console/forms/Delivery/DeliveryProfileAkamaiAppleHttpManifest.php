<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_DeliveryProfileAkamaiAppleHttpManifest extends Form_Delivery_DeliveryProfileConfiguration
{
	
	public function getAdvancedSettings()
	{
		$element = $this->addElement('checkbox', 'useTimingParameters', array(
				'label'			=> 'Use timing parameters:',
				'filters'		=> array('StringTrim'),
		));
		
		return array('useTimingParameters');
	}
	
}