<?php 
class Form_CreateDropFolder extends Zend_Form
{
	public function init()
	{
		$this->setAttrib('id', 'frmCreateDropFolder');
		$this->setDecorators(array(
			'FormElements', 
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));
		
				
		// submit button
		$this->addElement('button', 'submit', array(
			'ignore'	=> true,
			'label'		=> 'Create New',
			'onclick'		=> "configureDropFolder()",
			'decorators'	=> array('ViewHelper'),
		));
	}
}