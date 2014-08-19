<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_DeliveryProfileGenericHttp extends Form_Delivery_DeliveryProfileConfiguration
{
	
	public function getAdvancedSettings()
	{
		$this->addElement('text', 'pattern', array(
				'label'			=> 'Pattern:',
				'filters'		=> array('StringTrim'),
		));
		
		return array('pattern');
	}
	
}