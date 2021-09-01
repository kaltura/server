<?php 
/**
 * @package plugins.drm
 * @subpackage Admin
 */
class Form_CreateDrmPolicy extends Infra_Form
{
	public function init()
	{
		$this->setAttrib('id', 'frmCreateDrmPolicy');
		$this->setDecorators(array(
			'FormElements', 
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));
		
		$this->addElement('text', 'newPartnerId', array(
			'label'			=> 'Publisher ID:',
			'onkeypress'	=> "return supressFormSubmit(event)",
			'filters'		=> array('StringTrim'),
		));	

		$newDrmProvider = new Kaltura_Form_Element_EnumSelect('newDrmPolicyProvider', array('enum' => 'Kaltura_Client_Drm_Enum_DrmProviderType'));
		$newDrmProvider->setLabel('Provider:');
		$newDrmProvider->setRequired(true);
		$this->addElement($newDrmProvider);
				
		// submit button
		$this->addElement('button', 'submit', array(
			'ignore'	=> true,
			'label'		=> 'Create New',
			'onclick'		=> "newDrmPolicy($('#newPartnerId').val(), $('#newDrmPolicyProvider').val())",
			'decorators'	=> array('ViewHelper'),
		));
	}
}