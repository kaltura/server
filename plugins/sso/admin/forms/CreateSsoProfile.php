<?php 
/**
 * @package plugins.sso
 * @subpackage Admin
 */
class Form_CreateSsoProfile extends Infra_Form
{
	public function init()
	{
		$this->setAttrib('id', 'frmCreateSsoProfile');
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
		$this->addElement('button', 'newSsoProfile', array(
			'ignore' => true,
			'label' => 'Create New',
			'onclick' => "addSsoProfile($('#newPartnerId').val())",
			'decorators' => array('ViewHelper'),
		));
	}
}