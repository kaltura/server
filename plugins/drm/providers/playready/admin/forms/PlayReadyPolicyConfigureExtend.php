<?php
/**
 * @package plugins.playReady
 * @subpackage Admin
 * @abstract
 */
class Form_PlayReadyPolicyConfigureExtend_SubForm extends Form_DrmPolicyConfigureExtend_SubForm
{
	public function init()
	{
        $this->addElement('text', 'gracePeriod', array(
			'label'			=> 'Grace Period:',
			'filters'		=> array('StringTrim'),
		));

		$enumElement = new Kaltura_Form_Element_EnumSelect('licenseRemovalPolicy', array('enum' => 'Kaltura_Client_PlayReady_Enum_PlayReadyLicenseRemovalPolicy'));
		$enumElement->setLabel('License Removal Policy:');
		$this->addElements(array($enumElement));

		$this->addElement('text', 'licenseRemovalDuration', array(
			'label'			=> 'License Removal Duration:',
			'filters'		=> array('StringTrim'),
		));

		$enumElement = new Kaltura_Form_Element_EnumSelect('minSecurityLevel', array('enum' => 'Kaltura_Client_PlayReady_Enum_PlayReadyMinimumLicenseSecurityLevel'));
		$enumElement->setLabel('Minimum Security Level:');
		$this->addElements(array($enumElement));
	}
}