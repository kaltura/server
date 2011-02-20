<?php 
class Form_NewUIConf extends Zend_Form
{
	public function init()
	{
		$this->setAttrib('id', 'addNewUiConf');
		$this->setDecorators(array(
			'FormElements', 
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));
		
		// submit button
		$this->addElement('button', 'newUiConf', array(
			'label'		=> 'Create New',
			'onclick'		=> "doAction('newUiConf', $('#newPartnerId').val())",
			'decorators'	=> array('ViewHelper'),
		));
	}
}