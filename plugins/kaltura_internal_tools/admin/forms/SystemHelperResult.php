<?php 
class Form_SystemHelperResult extends Zend_Form
{
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('class', 'inline-form');
		$this->setAttrib('id', 'frmSystemHelperResults');

		
		
		$this->addElement('textarea', 'results', array(
			'label'			=> 'Results:',
			'cols'			=> 48,
			'rows'			=> 7,
			'filters'		=> array('StringTrim'),
		));
		
		
		$this->setDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'dl')),
			array('Description', array('placement' => 'prepend')),
			'Fieldset',
			'Form',
		));
	}
}