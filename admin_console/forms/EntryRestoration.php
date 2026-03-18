<?php
/**
 * @package Admin
 * @subpackage Entry
 */
class Form_EntryRestoration extends Infra_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'frmEntryRestoration');
		$this->setAttrib('enctype', 'multipart/form-data');

		$this->setDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'fieldset')),
			'Form',
		));


		// Input mode selector
		$inputMode = new Zend_Form_Element_Select('inputMode');
		$inputMode->setLabel('Input Mode')
			->setRequired(true)
			->setMultiOptions(array(
				'single'	=> 'Single Entry ID',
				'textarea'	=> 'Comma-separated or Line-separated List',
				'file'		=> 'File Upload (one entry ID per line)',
			))
			->setValue('textarea')
			->setDecorators(array('ViewHelper', 'Label', array('HtmlTag', array('tag' => 'div', 'class' => 'form-field'))));
		$this->addElement($inputMode);

		// Entry IDs textarea
		$entryIds = new Zend_Form_Element_Textarea('entryIds');
		$entryIds->setLabel('Entry IDs')
			->setAttrib('rows', 10)
		->setAttrib('cols', 1)
			->addFilter('StringTrim')
			->setAttrib('style', 'width: 600px !important; box-sizing: border-box;')
			->setDecorators(array('ViewHelper', 'Label', array('HtmlTag', array('tag' => 'div', 'class' => 'form-field', 'id' => 'entryIdsField'))));
		$this->addElement($entryIds);

		// File upload
		$this->addElement('file', 'entryFile', array(
			'label'			=> 'Upload File',
			'decorators'	=> array('File', 'Label', array('HtmlTag', array('tag' => 'div', 'class' => 'form-field', 'id' => 'entryFileField', 'style' => 'display:none;'))),
		));

		// Submit button
		$this->addElement('button', 'cmdSubmit', array(
			'type'			=> 'submit',
			'label'			=> 'Restore Entries',
			'decorators'	=> array('ViewHelper', array('HtmlTag', array('tag' => 'div', 'class' => 'form-field'))),
		));
	}
}