<?php 
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_ClonePartnerCatalogItems extends Infra_Form
{
	public function init()
	{
		$this->setAttrib('id', 'frmClonePartnerCatalogItems');
		$this->setDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));

		$this->addElement('text', 'fromPartnerId', array(
			'label' => 'From Publisher ID:',
			'filters' => array('StringTrim'),
		));

		$this->addElement('text', 'toPartnerId', array(
			'label' => 'To Publisher ID:',
			'filters' => array('StringTrim'),
		));

		// submit button
		$this->addElement('button', 'submitClonePartnerCatalogItems', array(
			'ignore' => true,
			'label' => 'Clone',
			'onclick' => "clonePartnerCatalogItems($('#fromPartnerId').val(),$('#toPartnerId').val())",
			'decorators' => array('ViewHelper'),
		));
	}
}