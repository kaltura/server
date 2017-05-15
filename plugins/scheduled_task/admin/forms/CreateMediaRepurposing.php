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

		$this->addComment("customFormExplain", "Create custom MR here:");
		
		$this->addElement('text', 'newPartnerId', array(
			'label'			=> 'Publisher ID:',
			'onkeypress'	=> "return supressFormSubmit(event)",
			'filters'		=> array('StringTrim'),
		));

		$options = array('Kaltura_Client_Type_MediaEntryFilter' => 'media filter', "N/A" => "NONE");
		$this->addElement('select', 'filterType', array(
			'label'			=> 'Filter Type:',
			'filters'		=> array('StringTrim'),
			'multiOptions'	=> $options,
			'value'			=> 'N/A',
		));


		$this->addEnumElement('Filter','newMR','Kaltura_Client_ScheduledTask_Enum_ObjectFilterEngineType');
		
		// submit button
		$this->addElement('button', 'submit', array(
			'ignore'	=> true,
			'label'		=> 'Create New',
			'onclick'		=> "newMediaRepurposing($('#newPartnerId').val(), $('#newMRFilter').val(), $('#filterType').val())",
			'decorators' => array('ViewHelper')
		));
	}
}