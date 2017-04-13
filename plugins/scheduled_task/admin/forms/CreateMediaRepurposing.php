<?php 
/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class Form_CreateMediaRepurposing extends ConfigureSubForm
{
	public function init()
	{
		$this->setAttrib('id', 'frmCreateMediaRepurposing');
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


		$this->addEnumElement('Filter','newMR','Kaltura_Client_ScheduledTask_Enum_ObjectFilterEngineType');
		
		// submit button
		$this->addElement('button', 'submit', array(
			'ignore'	=> true,
			'label'		=> 'Create New',
			'onclick'		=> "newMediaRepurposing($('#newPartnerId').val(), $('#newMRFilter').val())",
			'decorators' => array('ViewHelper')
		));
	}
}