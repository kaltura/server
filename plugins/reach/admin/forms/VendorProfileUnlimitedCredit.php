<?php
/**
 * @package Admin
 * @subpackage Reach
 */
class Form_VendorProfileUnlimitedCredit extends Form_VendorProfileCredit
{
	public function init()
	{
		parent::init();
		$this->removeElement("credit");
		$this->removeElement("overageCredit");
	}


}