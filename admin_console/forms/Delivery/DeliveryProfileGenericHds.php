<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_DeliveryProfileGenericHds extends Form_Delivery_DeliveryProfileConfiguration
{
	
	public function getAdvancedSettings()
	{
		$this->addElement('text', 'pattern', array(
				'label'			=> 'Pattern:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'rendererClass', array(
				'label'			=> 'Renderer Class:',
				'filters'		=> array('StringTrim'),
		));
		
		return array('pattern','rendererClass');
	}
	
}