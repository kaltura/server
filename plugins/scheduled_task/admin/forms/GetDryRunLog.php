<?php 
/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class Form_GetDryRunLog extends ConfigureSubForm
{
	public function init()
	{
		$this->setAttrib('id', 'frmGetDryRunLog');
		$this->setDecorators(array(
			'FormElements', 
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));
		
		$this->addTextElement('Dry Run Identification:', 'dryRunId', array('oninput'=> 'checkNumValid(this.value)'));
		// submit button
		$this->addElement('button', 'getDryRunLogSubmit', array(
			'label'		=> 'Get Dry Run Log',
			'onclick'		=> "getDryRunLog($('#dryRunId').val())",
			'decorators' => array('ViewHelper')
		));
	}
}