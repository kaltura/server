<?php 
class Form_NewStorage extends Zend_Form
{
	public function init()
	{
		$this->setAttrib('id', 'addNewStorage');
		$this->setDecorators(array(
			'FormElements', 
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));
		
				
		// submit button
		$this->addElement('button', 'newStorage', array(
			'label'		=> 'Create New',
			'onclick'		=> "doAction('newStorage', $('#newPartnerId').val())",
			'decorators'	=> array('ViewHelper'),
		));
	}
}