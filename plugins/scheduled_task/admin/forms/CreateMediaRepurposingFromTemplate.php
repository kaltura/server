<?php 
/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class Form_CreateMediaRepurposingFromTemplate extends ConfigureSubForm
{
	public function init()
	{
		$this->setAttrib('id', 'frmCreateMediaRepurposingFromTemplate');
		$this->setDecorators(array(
			'FormElements', 
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));

		$this->addComment("templateFormExplain", "Create MR from template here:");
		
		$this->addElement('text', 'newPartnerIdTemplate', array(
			'label'			=> 'Publisher ID:',
			'filters'		=> array('StringTrim'),
			'oninput'	=> 'checkNumValid(this.value)',
		));

		$options = $this->getTemplateOption();
		$this->addElement('select', 'template', array(
			'label'			=> 'Choose Template:',
			'filters'		=> array('StringTrim'),
			'multiOptions'	=> $options,
		));
		$this->getElement("template")->setValue("N/A");

		// submit button
		$this->addElement('button', 'submit', array(
			'ignore'	=> true,
			'label'		=> 'Create New',
			'onclick'		=> "newMediaRepurposingFromTemplate($('#newPartnerIdTemplate').val(), $('#template').val())",
			'decorators' => array('ViewHelper')
		));
	}
	
	private function getTemplateOption()
	{
		$options = array("N/A" => "NONE");
		$templates = MediaRepurposingUtils::getMrs(MediaRepurposingUtils::ADMIN_CONSOLE_PARTNER);
		foreach ($templates as $template)
			$options[$template->id] = $template->name;
		return $options;

	}
}