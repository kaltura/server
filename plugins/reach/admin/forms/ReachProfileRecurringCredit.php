<?php
/**
 * @package Admin
 * @subpackage Reach
 */
class Form_ReachProfileRecurringCredit extends Form_ReachProfileTimeFramedCredit
{
	public function init()
	{
		parent::init();
		$frequency = new Kaltura_Form_Element_EnumSelect('frequency', array('enum' => 'Kaltura_Client_Reach_Enum_VendorCreditRecurrenceFrequency'));
		$frequency->setRequired(true);
		$frequency->setLabel("Frequency:");
		$frequency->setValue(Kaltura_Client_Reach_Enum_VendorCreditRecurrenceFrequency::YEARLY);
		$this->addElement($frequency);
	}

}
