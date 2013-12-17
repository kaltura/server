<?php 
/**
 * @package plugins.drm
 * @subpackage Admin
 */
class Form_CreateDrmProfile extends Infra_Form
{
	public function init()
	{
		$this->setAttrib('id', 'frmCreateDrmProfile');
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

		$newDrmProfileProvider = new Kaltura_Form_Element_EnumSelect('newDrmProfileProvider', array(
			'enum' => 'Kaltura_Client_Drm_Enum_DrmProviderType'
		));
		
		$newDrmProfileProvider->setLabel('Provider:');
		$newDrmProfileProvider->setRequired(true);
		$this->addElement($newDrmProfileProvider);
				
		// submit button
		$this->addElement('button', 'submit', array(
			'ignore'	=> true,
			'label'		=> 'Create New',
			'onclick'		=> "newDrmProfile($('#newPartnerId').val(), $('#newDrmProfileProvider').val())",
			'decorators'	=> array('ViewHelper'),
		));
	}
}