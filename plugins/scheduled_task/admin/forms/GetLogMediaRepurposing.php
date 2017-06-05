<?php 
/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class Form_GetLogMediaRepurposing extends ConfigureSubForm
{
	public function init()
	{
		$this->setAttrib('id', 'frmGetLogMediaRepurposing');
		$this->setDecorators(array(
			'FormElements', 
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));

		$this->addTextElement('Publisher ID:', 'partnerId', array('oninput'	=> 'checkNumValid(this.value)'));
		$this->addTextElement('Media Repurposing ID:', 'MediaRepurposingId', array('oninput'=>'checkNumValid(this.value)'));

		$this->addTextElement('Start Date:', 'StartDate', array('oninput'=>'checkNumValid(this.value)'));
		$this->addTextElement('End Date:', 'EndDate', array('oninput'=>'checkNumValid(this.value)'));
		
		// submit button
		$this->addElement('button', 'getLogs', array(
			'ignore'	=> true,
			'label'		=> 'Get Log',
			'onclick'		=> "getLog($('#partnerId').val(), $('#MediaRepurposingId').val(), $('#StartDate').val(), $('#EndDate').val())",
			'decorators' => array('ViewHelper')
		));
	}
}