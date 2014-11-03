<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_DeliveryProfileGenericRtmp extends Form_Delivery_DeliveryProfileConfiguration
{
	
	public function getAdvancedSettings()
	{
		$this->addElement('text', 'prefix', array(
				'label'			=> 'Prefix:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('checkbox', 'enforceRtmpe', array(
				'label'			=> 'Enforce RtmpE:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'pattern', array(
				'label'			=> 'Pattern:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'rendererClass', array(
				'label'			=> 'Renderer Class:',
				'filters'		=> array('StringTrim'),
		));
		
		return array('prefix', 'enforceRtmpe', 'pattern', 'rendererClass');
	}
	
}