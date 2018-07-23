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
		
		$this->addElement('text', 'toDate', array(
			'label'			=> 'To Date: (YYYY.MM.DD)',
			'innerType'     => 'DateElement',
			'required'      => true,
			'filters'		=> array('StringTrim'),
			'validators' => array('Int'),
			'oninput'	=> 'checkNumValid(this.value)'
		));
	}


}