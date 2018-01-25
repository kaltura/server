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
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));
	}
}