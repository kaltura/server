<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_VendorProfileNullCredit extends Form_VendorProfileCredit
{
	public function init()
	{
 		parent::init();
 		$this->removeElement("credit");
		$this->removeElement("overageCredit");
 		$this->removeElement("fromDate");

		$this->addElement('hidden', 'type', array(
			'filters' 		=> array('StringTrim'),
			'required'      => true,
			'validators' => array('Int'),
		));

	}

}