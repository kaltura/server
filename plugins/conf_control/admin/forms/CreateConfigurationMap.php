<?php 
/**
 * @package plugins.confControl
 * @subpackage Admin
 */
class Form_CreateConfigurationMap extends Infra_Form
{
	public function init()
	{
		$this->setAttrib('id', 'frmCreateConfigurationMap');
		$this->setDecorators(array(
			'FormElements',
			array('Form', array('class' => 'simple')),
		));
	}
}