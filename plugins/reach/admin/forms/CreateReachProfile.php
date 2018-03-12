<?php 
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_CreateReachProfile extends Infra_Form
{
	public function init()
	{
		$this->setAttrib('id', 'frmCreateReachProfile');
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
		$this->addElement('button', 'newReachProfile', array(
			'ignore' => true,
			'label' => 'Create New',
			'onclick' => "addReachProfile($('#newPartnerId').val())",
			'decorators' => array('ViewHelper'),
		));
	}
}