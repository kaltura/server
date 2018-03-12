<?php
/**
 * @package Admin
 * @subpackage Reach
 */
class Form_ReachProfileUnlimitedCredit extends Form_ReachProfileCredit
{
	public function init()
	{
		parent::init();
		$this->removeElement("credit");
		$this->removeElement("overageCredit");
	}


}