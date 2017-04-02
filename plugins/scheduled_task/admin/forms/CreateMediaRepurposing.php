<?php 
/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class Form_CreateMediaRepurposing extends ConfigureForm
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

		$newMediaRepurposingType = new Kaltura_Form_Element_EnumSelect('newMediaRepurposingType', array(
			'enum' => 'Kaltura_Client_ScheduledTask_Enum_ObjectTaskType',
			'excludes' => array(
			)
		));

		$newMediaRepurposingType->setLabel('Type:');
		$newMediaRepurposingType->setRequired(true);
		$this->addElement($newMediaRepurposingType);



		$this->addEnumElement('Filter','newMR','Kaltura_Client_ScheduledTask_Enum_ObjectFilterEngineType');
		
		// submit button
		$this->addElement('button', 'submit', array(
			'ignore'	=> true,
			'label'		=> 'Create New',
			'onclick'		=> "newMediaRepurposing($('#newPartnerId').val(), $('#newMediaRepurposingType').val(), $('#newMRFilter').val())",
			'decorators' => array('ViewHelper')
		));
	}
}