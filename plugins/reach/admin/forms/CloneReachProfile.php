<?php 
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_CloneReachProfile extends Infra_Form
{
	public function init()
	{
		$this->setAttrib('id', 'frmCloneReachProfile');
		$this->setDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));

		$this->addElement('text', 'toPartnerId', array(
			'label' => 'To Publisher ID:',
			'filters' => array('StringTrim'),
		));

		$this->addElement('text', 'profileId', array(
			'label' => 'Profile ID:',
			'filters' => array('StringTrim'),
		));

		// submit button
		$this->addElement('button', 'submitCloneReachProfile', array(
			'ignore' => true,
			'label' => 'Clone Profile',
			'onclick' => "cloneReachProfile($('#toPartnerId').val(),$('#profileId').val())",
			'decorators' => array('ViewHelper'),
		));
	}
}