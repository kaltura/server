<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_CreateEntryVendorTask extends Infra_Form
{
	public function init()
	{
		$this->setAttrib('id', 'frmCreateEntryVendorTask');
		$this->setDecorators(array(
			'FormElements',
			array('Form', array('class' => 'simple')),
		));
	}
}