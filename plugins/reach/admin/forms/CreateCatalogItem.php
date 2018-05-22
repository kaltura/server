<?php 
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_CreateCatalogItem extends Infra_Form
{
	public function init()
	{
		$this->setAttrib('id', 'frmCreateCatalogItem');
		$this->setDecorators(array(
			'FormElements',
			array('Form', array('class' => 'simple')),
		));
	}
}