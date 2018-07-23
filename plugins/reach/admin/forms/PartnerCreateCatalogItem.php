<?php 
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_PartnerCreateCatalogItem extends Infra_Form
{
	public function init()
	{
		$this->setAttrib('id', 'frmCreatePartnerCatalogItem');
		$this->setDecorators(array(
			'FormElements',
			array('Form', array('class' => 'simple')),
		));

	}
}