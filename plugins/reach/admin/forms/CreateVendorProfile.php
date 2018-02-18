<?php 
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_CreateVendorProfile extends Infra_Form
{
	public function init()
	{
		$this->setAttrib('id', 'frmCreateVendorProfile');
		$this->setDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));

		$this->addElement('text', 'newPartnerId', array(
			'label' => 'Publisher ID:',
			'filters' => array('StringTrim'),
		));

		// submit button
		$this->addElement('button', 'newVendorProfile', array(
			'ignore' => true,
			'label' => 'Create New',
			'onclick' => "addVendorProfile($('#newPartnerId').val())",
			'decorators' => array('ViewHelper'),
		));
	}
}