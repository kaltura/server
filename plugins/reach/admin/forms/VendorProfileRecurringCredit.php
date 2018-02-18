<?php
/**
 * @package Admin
 * @subpackage Reach
 */
class Form_VendorProfileRecurringCredit extends Form_VendorProfileTimeFramedCredit
{
	public function init()
	{
		parent::init();
		$frequency = new Kaltura_Form_Element_EnumSelect('priceFunction', array('enum' => 'Kaltura_Client_Reach_Enum_VendorCreditRecurrenceFrequency'));
		$frequency->setRequired(true);
		$frequency->setLabel("Frequency:");
		$frequency->setValue(Kaltura_Client_Reach_Enum_VendorCreditRecurrenceFrequency::MONTHLY);
		$this->addElement($frequency);
	}

}