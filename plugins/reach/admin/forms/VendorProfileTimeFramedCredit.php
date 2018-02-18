<?php
/**
 * @package Admin
 * @subpackage Reach
 */
class Form_VendorProfileTimeFramedCredit extends Form_VendorProfileCredit
{
	
	public function init()
	{
		parent::init();
		$this->addElement('text', 'toDate', array(
				'label'			=> 'To Date: (MM.DD.YYYY)',
				'innerType'     => 'DateElement',
				'required'      => true,
				'filters'		=> array('StringTrim'),
				'validators' => array('Int'),
				'oninput'	=> 'checkNumValid(this.value)'
		));
		$this->setDefault('toDate',  "Enter Date");
	}



}